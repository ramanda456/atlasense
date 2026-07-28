<?php

namespace App\Services;

use App\Models\KursMataUang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PenganalisaKurs
{
    /**
     * Ambil rate realtime dari ExchangeRate-API
     */
    public function ambilKurs(string $base = 'USD', string $target = 'IDR', bool $paksa = false): ?KursMataUang
    {
        $base = strtoupper($base);
        $target = strtoupper($target);
        $cached = KursMataUang::where('base_currency', $base)
            ->where('target_currency', $target)
            ->latest('recorded_at')
            ->first();

        if (!$paksa
            && $cached
            && $cached->source !== 'Data Demo Seeder'
            && $cached->recorded_at?->gt(now()->subHours(6))) {
            return $cached;
        }

        $url = rtrim(config('services.exchange_rate.url'), '/') . '/latest/' . $base;
        $mulai = microtime(true);

        try {
            $response = Http::timeout(20)->retry(2, 400)->get($url);
            PelacakApi::catat('ExchangeRate-API', $url, $response->status(), $response->successful(), $mulai);
            $response->throw();

            $rate = $response->json('rates.' . $target);
            if (!$rate) {
                return $cached;
            }

            $previous = $cached;
            $change = $previous && $previous->rate > 0
                ? (($rate - $previous->rate) / $previous->rate) * 100
                : 0;
            $updatedAt = $response->json('time_last_update_unix');
            $recordedAt = $updatedAt ? now()->setTimestamp((int) $updatedAt) : now();

            return KursMataUang::updateOrCreate(
                [
                    'base_currency' => $base,
                    'target_currency' => $target,
                    'rate_date' => $recordedAt->toDateString(),
                    'source' => 'ExchangeRate-API',
                ],
                [
                    'rate' => $rate,
                    'change_percent' => round($change, 4),
                    'recorded_at' => $recordedAt,
                ]
            );
        } catch (\Throwable $e) {
            PelacakApi::catat('ExchangeRate-API', $url, 0, false, $mulai, $e->getMessage());
            return $cached;
        }
    }

    /**
     * Ambil histori nilai tukar dari Frankfurter API untuk grafik tren
     */
    public function histori(string $base = 'USD', string $target = 'IDR', int $hari = 30, bool $paksa = false): Collection
    {
        $base = strtoupper($base);
        $target = strtoupper($target);
        $hari = max(7, min($hari, 365));
        $dari = now()->subDays($hari)->toDateString();
        $ke = now()->toDateString();

        $yangAda = $this->historiTersimpan($base, $target, $dari);
        $adaKursAsli = $yangAda->contains(fn (KursMataUang $rate) => $rate->source !== 'Data Demo Seeder');

        if (!$paksa && $adaKursAsli && $yangAda->count() >= min(10, (int) ($hari / 2))) {
            return $yangAda;
        }

        $url = rtrim(config('services.frankfurter.url'), '/') . '/rates';
        $params = ['from' => $dari, 'to' => $ke, 'base' => $base, 'quotes' => $target];
        $mulai = microtime(true);

        try {
            $response = Http::timeout(25)->retry(2, 400)->get($url, $params);
            PelacakApi::catat('Frankfurter Historical FX', $url . '?' . http_build_query($params), $response->status(), $response->successful(), $mulai);
            $response->throw();

            $payload = $response->json();
            $rows = array_is_list($payload) ? $payload : [];

            if (!$rows && isset($payload['rates']) && is_array($payload['rates'])) {
                foreach ($payload['rates'] as $date => $rates) {
                    $rows[] = ['date' => $date, 'base' => $payload['base'] ?? $base, 'quote' => $target, 'rate' => $rates[$target] ?? null];
                }
            }

            $previousRate = null;
            foreach ($rows as $row) {
                $quote = strtoupper($row['quote'] ?? $row['currency'] ?? $target);
                $rowBase = strtoupper($row['base'] ?? $base);
                $date = $row['date'] ?? null;
                $value = $row['rate'] ?? null;

                if ($rowBase !== $base || $quote !== $target || !$date || !is_numeric($value)) {
                    continue;
                }

                $change = $previousRate && $previousRate > 0 ? (($value - $previousRate) / $previousRate) * 100 : 0;
                
                KursMataUang::updateOrCreate(
                    ['base_currency' => $base, 'target_currency' => $target, 'rate_date' => $date, 'source' => 'Frankfurter'],
                    ['rate' => $value, 'change_percent' => round($change, 4), 'recorded_at' => $date . ' 12:00:00']
                );
                $previousRate = (float) $value;
            }
        } catch (\Throwable $e) {
            PelacakApi::catat('Frankfurter Historical FX', $url, 0, false, $mulai, $e->getMessage());
        }

        // Jalankan fetch rate realtime terupdate
        $this->ambilKurs($base, $target);

        return $this->historiTersimpan($base, $target, $dari);
    }

    /**
     * Dapatkan histori kurs yang tersimpan di DB
     */
    private function historiTersimpan(string $base, string $target, string $dari): Collection
    {
        return KursMataUang::where('base_currency', $base)
            ->where('target_currency', $target)
            ->whereDate('rate_date', '>=', $dari)
            ->orderBy('rate_date')
            ->get()
            ->groupBy(fn (KursMataUang $rate) => (string) $rate->rate_date)
            ->map(fn (Collection $sameDate) => $sameDate
                ->sortBy(fn (KursMataUang $rate) => $rate->source === 'Data Demo Seeder' ? 1 : 0)
                ->first())
            ->sortBy('rate_date')
            ->values();
    }

    /**
     * Konversi nominal mata uang
     */
    public function konversi(float $nominal, string $base, string $target): ?float
    {
        $rate = $this->ambilKurs($base, $target);
        return $rate ? round($nominal * $rate->rate, 2) : null;
    }
}
