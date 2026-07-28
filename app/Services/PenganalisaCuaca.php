<?php

namespace App\Services;

use App\Models\Negara;
use App\Models\DataCuaca;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PenganalisaCuaca
{
    /**
     * Mengambil data cuaca saat ini dari Open-Meteo API
     */
    public function saatIni(Negara $negara, bool $paksa = false): ?DataCuaca
    {
        $cached = $negara->weatherHistory()->latest('observed_at')->first();
        if (!$paksa && $cached && $cached->observed_at?->gt(now()->subMinutes(30))) {
            return $cached;
        }
        if ($negara->latitude === null || $negara->longitude === null) {
            return $cached;
        }

        $url = rtrim(config('services.open_meteo.url'), '/') . '/forecast';
        $params = [
            'latitude' => $negara->latitude,
            'longitude' => $negara->longitude,
            'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,wind_speed_10m,wind_gusts_10m,weather_code,is_day',
            'hourly' => 'precipitation_probability',
            'forecast_hours' => 1,
            'timezone' => 'auto',
        ];
        $mulai = microtime(true);

        try {
            $response = Http::timeout(20)->retry(2, 400)->get($url, $params);
            PelacakApi::catat('Open-Meteo', $url . '?' . http_build_query($params), $response->status(), $response->successful(), $mulai);
            $response->throw();

            $current = $response->json('current', []);
            $wind = (float) ($current['wind_speed_10m'] ?? 0);
            $gust = (float) ($current['wind_gusts_10m'] ?? $wind);
            $rain = (float) ($current['precipitation'] ?? 0);
            $probability = (float) ($response->json('hourly.precipitation_probability.0') ?? 0);
            $weatherCode = (int) ($current['weather_code'] ?? 0);
            
            // Tambahan penalti jika ada badai petir sesuai kode cuaca WMO
            $stormCodeBonus = in_array($weatherCode, [95, 96, 99], true) ? 30 : 0;
            
            // Formula kustom storm risk
            $storm = min(100, ($wind * .55) + ($gust * .35) + min(20, $rain * 4) + ($probability * .15) + $stormCodeBonus);

            return DataCuaca::create([
                'country_id' => $negara->id,
                'temperature' => $current['temperature_2m'] ?? null,
                'apparent_temperature' => $current['apparent_temperature'] ?? null,
                'humidity' => $current['relative_humidity_2m'] ?? null,
                'precipitation' => $rain,
                'precipitation_probability' => $probability,
                'wind_speed' => $wind,
                'wind_gust' => $gust,
                'weather_code' => $weatherCode,
                'condition' => $this->kondisi($weatherCode),
                'is_day' => isset($current['is_day']) ? (bool) $current['is_day'] : null,
                'storm_risk' => round($storm, 2),
                'observed_at' => $current['time'] ?? now(),
            ]);
        } catch (\Throwable $e) {
            PelacakApi::catat('Open-Meteo', $url, 0, false, $mulai, $e->getMessage());
            return $cached;
        }
    }

    /**
     * Ambil daftar cuaca terbaru dari DB
     */
    public function ikhtisar(int $limit = 50): Collection
    {
        return DataCuaca::query()
            ->with('negara')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')->from('weather_data')->groupBy('country_id');
            })
            ->latest('observed_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Sinkronisasi overview cuaca untuk negara prioritas
     */
    public function sinkronIkhtisar(int $limit = 35): Collection
    {
        @set_time_limit(120);
        $negaraList = Negara::whereNotNull('latitude')->whereNotNull('longitude')->limit($limit)->get();

        foreach ($negaraList as $negara) {
            try {
                $this->saatIni($negara);
            } catch (\Throwable $e) {}
        }
        return $this->ikhtisar($limit);
    }

    /**
     * Konversi kode cuaca WMO ke deskripsi bahasa Indonesia
     */
    public function kondisi(int $code): string
    {
        return match (true) {
            $code === 0 => 'Cerah',
            in_array($code, [1, 2, 3], true) => 'Berawan',
            in_array($code, [45, 48], true) => 'Berkabut',
            in_array($code, [51, 53, 55, 56, 57], true) => 'Gerimis',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82], true) => 'Hujan',
            in_array($code, [71, 73, 75, 77, 85, 86], true) => 'Salju',
            in_array($code, [95, 96, 99], true) => 'Badai Petir',
            default => 'Tidak diketahui',
        };
    }
}
