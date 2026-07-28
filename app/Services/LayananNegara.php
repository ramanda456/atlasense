<?php

namespace App\Services;

use App\Models\Negara;
use App\Models\EkonomiNegara;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LayananNegara
{
    /**
     * Sinkronisasi data negara dari REST Countries API
     */
    public function sinkronNegara(): int
    {
        $key = config('services.rest_countries.key');

        if ($key) {
            $jumlah = $this->sinkronV5($key);
            if ($jumlah > 0) {
                return $jumlah;
            }
        }

        return $this->sinkronLegacy();
    }

    /**
     * Ambil data dari REST Countries v5 (Berbayar/API Key)
     */
    private function sinkronV5(string $key): int
    {
        $baseUrl = rtrim(config('services.rest_countries.url'), '/');
        $offset = 0;
        $jumlah = 0;
        $adaLagi = true;

        while ($adaLagi && $offset < 500) {
            $url = $baseUrl;
            $params = [
                'limit' => 100,
                'offset' => $offset,
                'response_fields_omit' => 'names.translations,leaders',
            ];
            $mulai = microtime(true);

            try {
                $response = Http::withToken($key)->timeout(30)->retry(2, 500)->get($url, $params);
                PelacakApi::catat('REST Countries v5', $url . '?' . http_build_query($params), $response->status(), $response->successful(), $mulai);
                $response->throw();

                $objects = $response->json('data.objects', []);
                foreach ($objects as $item) {
                    if ($this->simpanNegara($item)) {
                        $jumlah++;
                    }
                }

                $adaLagi = (bool) $response->json('data.meta.more', false);
                $offset += 100;
            } catch (\Throwable $e) {
                PelacakApi::catat('REST Countries v5', $url, 0, false, $mulai, $e->getMessage());
                break;
            }
        }

        if ($jumlah > 0) {
            Cache::forget('countries.list');
        }

        return $jumlah;
    }

    /**
     * Fallback ke REST Countries v3.1 (Legacy/Gratis)
     */
    private function sinkronLegacy(): int
    {
        $url = env('REST_COUNTRIES_LEGACY_URL', 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/json/countries.json');
        $mulai = microtime(true);

        try {
            $response = Http::timeout(30)->retry(2, 500)->get($url);
            PelacakApi::catat('Countries DB CDN', $url, $response->status(), $response->successful(), $mulai);
            $response->throw();

            $jumlah = 0;
            foreach ($response->json() as $item) {
                if ($this->simpanNegaraDr5hn($item) || $this->simpanNegara($item)) {
                    $jumlah++;
                }
            }

            Cache::forget('countries.list');
            return $jumlah;
        } catch (\Throwable $e) {
            PelacakApi::catat('Countries DB CDN', $url, 0, false, $mulai, $e->getMessage());
            return 0;
        }
    }

    /**
     * Mapping dan simpan data negara dari dataset dr5hn (Populasi 100% Akurat)
     */
    private function simpanNegaraDr5hn(array $item): bool
    {
        $code = $item['iso2'] ?? null;
        if (!$code || strlen($code) !== 2) {
            return false;
        }

        $flagUrl = 'https://flagcdn.com/' . strtolower($code) . '.svg';

        Negara::updateOrCreate(
            ['code' => strtoupper($code)],
            [
                'name' => $item['name'] ?? $code,
                'official_name' => $item['name'] ?? $code,
                'cca3' => $item['iso3'] ?? null,
                'region' => $item['region'] ?? null,
                'subregion' => $item['subregion'] ?? null,
                'capital' => $item['capital'] ?? null,
                'currency_code' => $item['currency'] ?? null,
                'currency_name' => $item['currency_name'] ?? null,
                'language' => $item['nationality'] ?? null,
                'flag_url' => $flagUrl,
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
                'population' => (int) ($item['population'] ?? 0),
            ]
        );

        return true;
    }

    /**
     * Mapping dan simpan data negara ke database
     */
    private function simpanNegara(array $item): bool
    {
        $code = data_get($item, 'codes.alpha_2') ?: ($item['cca2'] ?? null);
        if (!$code || strlen($code) !== 2) {
            return false;
        }

        $currencies = $item['currencies'] ?? [];
        $currencyCode = null;
        $currencyName = null;

        if (is_array($currencies) && $currencies !== []) {
            if (array_is_list($currencies)) {
                $currencyCode = data_get($currencies, '0.code') ?: data_get($currencies, '0.iso_code');
                $currencyName = data_get($currencies, '0.name');
            } else {
                $currencyCode = array_key_first($currencies);
                $currencyName = data_get($currencies, $currencyCode . '.name');
            }
        }

        $languages = $item['languages'] ?? [];
        if (array_is_list($languages)) {
            $languageNames = collect($languages)->map(fn ($language) => is_array($language) ? ($language['name'] ?? $language['english_name'] ?? null) : $language)->filter();
        } else {
            $languageNames = collect(array_values($languages))->map(fn ($language) => is_array($language) ? ($language['name'] ?? null) : $language)->filter();
        }

        $capital = $item['capitals'] ?? ($item['capital'] ?? []);
        $capital = is_array($capital) ? collect($capital)->map(fn ($value) => is_array($value) ? ($value['name'] ?? null) : $value)->filter()->implode(', ') : $capital;

        $latitude = data_get($item, 'coordinates.lat') ?? data_get($item, 'latlng.0');
        $longitude = data_get($item, 'coordinates.lng') ?? data_get($item, 'latlng.1');

        $flagUrl = data_get($item, 'flag.url_svg')
            ?: data_get($item, 'flag.url_png')
            ?: data_get($item, 'flag.svg')
            ?: data_get($item, 'flag.png')
            ?: data_get($item, 'flags.svg')
            ?: data_get($item, 'flags.png')
            ?: 'https://flagcdn.com/' . strtolower($code) . '.svg';

        Negara::updateOrCreate(
            ['code' => strtoupper($code)],
            [
                'name' => data_get($item, 'names.common') ?: data_get($item, 'name.common') ?: $code,
                'official_name' => data_get($item, 'names.official') ?: data_get($item, 'name.official'),
                'cca3' => data_get($item, 'codes.alpha_3') ?: ($item['cca3'] ?? null),
                'region' => $item['region'] ?? null,
                'subregion' => $item['subregion'] ?? null,
                'capital' => $capital,
                'currency_code' => $currencyCode,
                'currency_name' => $currencyName,
                'language' => $languageNames->implode(', '),
                'flag_url' => $flagUrl,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'population' => $item['population'] ?? null,
            ]
        );

        return true;
    }

    /**
     * Mengambil riwayat ekonomi negara dari World Bank API
     */
    public function riwayatEkonomi(Negara $negara, int $tahun = 10, bool $paksa = false): Collection
    {
        $tahun = max(3, min($tahun, 30));
        $yangAda = $negara->economics()->orderBy('year')->get();
        $masihBaru = $yangAda->count() >= min(5, $tahun)
            && optional($yangAda->max('updated_at'))->gt(now()->subDays(7));

        if (!$paksa && $masihBaru) {
            return $yangAda->take(-$tahun)->values();
        }

        $tahunSelesai = (int) now()->format('Y');
        $tahunMulai = $tahunSelesai - $tahun;
        $indikator = [
            'NY.GDP.MKTP.CD' => 'gdp',
            'FP.CPI.TOTL.ZG' => 'inflation',
            'NE.EXP.GNFS.CD' => 'exports',
            'NE.IMP.GNFS.CD' => 'imports',
            'SP.POP.TOTL' => 'population',
        ];
        $perTahun = [];

        foreach ($indikator as $ind => $field) {
            $url = rtrim(config('services.world_bank.url'), '/') . '/country/' . $negara->code . '/indicator/' . $ind;
            $params = [
                'format' => 'json',
                'date' => $tahunMulai . ':' . $tahunSelesai,
                'per_page' => 100,
            ];
            $mulai = microtime(true);

            try {
                $response = Http::timeout(25)->retry(2, 400)->get($url, $params);
                PelacakApi::catat('World Bank', $url . '?' . http_build_query($params), $response->status(), $response->successful(), $mulai);
                if (!$response->successful()) {
                    continue;
                }

                foreach ($response->json('1', []) as $row) {
                    if (($row['value'] ?? null) === null || !is_numeric($row['date'] ?? null)) {
                        continue;
                    }
                    $thn = (int) $row['date'];
                    $perTahun[$thn][$field] = $row['value'];
                }
            } catch (\Throwable $e) {
                PelacakApi::catat('World Bank', $url, 0, false, $mulai, $e->getMessage());
            }
        }

        foreach ($perTahun as $thn => $values) {
            EkonomiNegara::updateOrCreate(
                ['country_id' => $negara->id, 'year' => $thn],
                array_merge($values, ['country_id' => $negara->id, 'year' => $thn])
            );
        }

        return $negara->economics()->orderBy('year')->get()->take(-$tahun)->values();
    }
}
