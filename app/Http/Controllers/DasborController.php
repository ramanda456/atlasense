<?php

namespace App\Http\Controllers;

use App\Models\Negara;
use App\Models\Pelabuhan;
use App\Models\CacheBerita;
use App\Models\DaftarPantauan;
use App\Models\SkorRisiko;
use App\Models\LogApi;
use App\Models\DataCuaca;
use App\Models\KursMataUang;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DasborController extends Controller
{
    public function index(): View
    {
        if (Negara::count() < 50) {
            try {
                app(\App\Services\LayananNegara::class)->sinkronNegara();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if (\App\Models\EkonomiNegara::count() < 10) {
            try {
                \Illuminate\Support\Facades\Artisan::call('atlasense:sinkron-ekonomi');
                \Illuminate\Support\Facades\Artisan::call('atlasense:sinkron-kurs');
                \Illuminate\Support\Facades\Artisan::call('atlasense:sinkron-cuaca');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return view('dasbor', $this->dashboardData());
    }

    public function live(): JsonResponse
    {
        $data = $this->dashboardData();

        return response()->json([
            'success' => true,
            'message' => 'Dasbor berhasil diperbarui secara realtime.',
            'data' => [
                'counts' => [
                    'countries' => $data['negaraCount'],
                    'ports' => $data['pelabuhanCount'],
                    'news' => $data['beritaCount'],
                    'watchlists' => $data['pantauanCount'],
                ],
                'top_risks' => $data['topRisks']->map(fn ($risk) => [
                    'id' => $risk->id,
                    'country_name' => $risk->negara?->name ?? '-',
                    'country_code' => $risk->negara?->code,
                    'total_score' => (float) $risk->total_score,
                    'risk_level' => $risk->risk_level,
                    'calculated_human' => $risk->created_at ? $risk->created_at->diffForHumans() : '-',
                ])->values(),
                'recent_news' => $data['recentNews']->map(fn ($news) => [
                    'id' => $news->id,
                    'title' => $news->title,
                    'source' => $news->source,
                    'sentiment' => $news->sentiment,
                    'published_human' => $news->published_at ? $news->published_at->diffForHumans() : '-',
                ])->values(),
                'api_health' => $data['apiHealth']->map(fn ($log) => [
                    'service' => $log->service,
                    'status_code' => $log->status_code,
                    'success' => $log->success,
                    'response_time_ms' => $log->response_time_ms,
                    'requested_human' => $log->requested_at ? $log->requested_at->diffForHumans() : '-',
                ])->values(),
                'risk_levels' => $data['riskLevels'],
                'refreshed_label' => now()->translatedFormat('d F Y, H:i:s'),
                'latest_data_label' => $data['latestDataAt'] ? $data['latestDataAt']->translatedFormat('d F Y, H:i:s') : '-',
            ]
        ]);
    }

    private function dashboardData(): array
    {
        $latestRiskIds = SkorRisiko::selectRaw('MAX(id)')->groupBy('country_id');
        $latestRisks = SkorRisiko::with('negara')->whereIn('id', $latestRiskIds)->get();

        $topRisks = $latestRisks->sortByDesc(fn ($r) => (float) $r->total_score)->take(5)->values();

        $distribution = ['Rendah' => 0, 'Sedang' => 0, 'Tinggi' => 0];
        foreach ($latestRisks as $r) {
            if (array_key_exists($r->risk_level, $distribution)) {
                $distribution[$r->risk_level]++;
            }
        }

        $recentNews = CacheBerita::latest('published_at')->limit(5)->get();

        $latestApiLogIds = LogApi::selectRaw('MAX(id)')->groupBy('service');
        $apiHealth = LogApi::whereIn('id', $latestApiLogIds)->orderByDesc('requested_at')->limit(6)->get();

        $dates = collect([
            SkorRisiko::max('created_at'),
            CacheBerita::max('published_at'),
            LogApi::max('requested_at'),
            DataCuaca::max('observed_at'),
            KursMataUang::max('recorded_at'),
        ])->filter()->map(fn ($d) => Carbon::parse($d))->sortDesc();

        return [
            'negaraCount' => Negara::count(),
            'pelabuhanCount' => Pelabuhan::count(),
            'beritaCount' => CacheBerita::count(),
            'pantauanCount' => DaftarPantauan::where('user_id', /** @var \App\Models\User */ auth()->user()->id)->count(),
            'topRisks' => $topRisks,
            'recentNews' => $recentNews,
            'apiHealth' => $apiHealth,
            'riskLevels' => $distribution,
            'latestDataAt' => $dates->first(),
        ];
    }
}
