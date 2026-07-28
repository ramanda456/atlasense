<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataCuaca;

class PantauCuacaController extends Controller
{
    public function index(Request $request)
    {
        // Incremental batching untuk 250 negara tanpa bikin page timeout
        if (\App\Models\DataCuaca::select('country_id')->distinct()->count() < 250) {
            try {
                @set_time_limit(60);
                $unweathered = \App\Models\Negara::whereNotIn('id', function($q) {
                    $q->select('country_id')->from('weather_data');
                })->whereNotNull('latitude')->whereNotNull('longitude')->limit(40)->get();

                foreach ($unweathered as $n) {
                    try {
                        app(\App\Services\PenganalisaCuaca::class)->saatIni($n, true);
                    } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {}
        }

        $query = DataCuaca::with('negara');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('negara', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Ambil data terbaru untuk tiap negara
        $latestIds = DataCuaca::selectRaw('MAX(id)')->groupBy('country_id');
        $query->whereIn('id', $latestIds);

        $weatherData = $query->orderBy('storm_risk', 'desc')->paginate(12);
        
        // Seluruh data cuaca untuk pins di peta
        $allWeather = DataCuaca::with('negara')->whereIn('id', $latestIds)->get();

        return view('pantau.cuaca', compact('weatherData', 'allWeather'));
    }
}
