<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negara;
use App\Models\DaftarPantauan;

class NegaraController extends Controller
{
    public function index(Request $request)
    {
        if (Negara::where('population', 0)->orWhereNull('population')->count() > 5) {
            try {
                app(\App\Services\LayananNegara::class)->sinkronNegara();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $query = Negara::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
            });
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $countries = $query->orderBy('name')->paginate(12);
        $regions = Negara::whereNotNull('region')->distinct()->pluck('region')->filter();

        return view('negara.indeks', compact('countries', 'regions'));
    }

    public function show(string $code)
    {
        $country = Negara::with([
            'economics' => fn($q) => $q->orderBy('year', 'asc'),
            'weatherHistory' => fn($q) => $q->orderBy('observed_at', 'desc')->limit(10),
            'risks' => fn($q) => $q->orderBy('calculated_at', 'asc')->limit(15),
            'ports',
            'latestEconomic',
            'latestWeather',
            'latestRisk'
        ])->where('code', strtoupper($code))->firstOrFail();

        // 1. Auto-fetch riwayat ekonomi (GDP & Inflasi) jika belum ada
        if ($country->economics->isEmpty()) {
            try {
                app(\App\Services\LayananNegara::class)->riwayatEkonomi($country, 10);
            } catch (\Throwable $e) {}
        }

        // 2. Auto-create pelabuhan jika belum ada
        if ($country->ports->isEmpty()) {
            try {
                \App\Models\Pelabuhan::firstOrCreate(
                    ['unlocode' => $country->code . 'PRT'],
                    [
                        'country_id' => $country->id,
                        'name' => 'Pelabuhan ' . ($country->capital ?: $country->name),
                        'city' => $country->capital ?: $country->name,
                        'country_name' => $country->name,
                        'latitude' => $country->latitude ?: 0,
                        'longitude' => $country->longitude ?: 0,
                        'port_type' => 'Pelabuhan Utama',
                        'status' => 'Aktif',
                        'data_source' => 'AtlaSense Global'
                    ]
                );
            } catch (\Throwable $e) {}
        }

        // 3. Auto-hitung skor risiko jika belum ada
        if ($country->latestRisk === null) {
            try {
                app(\App\Services\PenilaiAncaman::class)->hitung($country);
            } catch (\Throwable $e) {}
        }

        // Reload relasi agar data terbaru yang baru di-fetch langsung tampil di view
        $country->load([
            'economics' => fn($q) => $q->orderBy('year', 'asc'),
            'weatherHistory' => fn($q) => $q->orderBy('observed_at', 'desc')->limit(10),
            'risks' => fn($q) => $q->orderBy('calculated_at', 'asc')->limit(15),
            'ports',
            'latestEconomic',
            'latestWeather',
            'latestRisk'
        ]);

        // 4. Ambil berita logistik terupdate untuk negara ini
        $news = \App\Models\CacheBerita::where('country_id', $country->id)->latest('published_at')->limit(5)->get();
        if ($news->isEmpty()) {
            try {
                app(\App\Services\PenganalisaBerita::class)->ambilBerita($country, 'logistics shipping trade economy', 5);
                $news = \App\Models\CacheBerita::where('country_id', $country->id)->latest('published_at')->limit(5)->get();
            } catch (\Throwable $e) {}
        }

        // Cek apakah ada di daftar pantauan (watchlist)
        $isWatched = DaftarPantauan::where('user_id', auth()->user()->id)
            ->where('country_id', $country->id)
            ->exists();

        return view('negara.detail', compact('country', 'news', 'isWatched'));
    }
}
