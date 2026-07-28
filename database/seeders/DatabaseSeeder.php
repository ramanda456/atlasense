<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Negara;
use App\Models\Pelabuhan;
use App\Models\KataPositif;
use App\Models\KataNegatif;
use App\Models\PengaturanSistem;
use App\Models\Artikel;
use App\Models\EkonomiNegara;
use App\Models\KursMataUang;
use App\Models\DataCuaca;
use App\Models\CacheBerita;
use App\Models\SentimenBerita;
use App\Models\SkorRisiko;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Default
        $admin = User::updateOrCreate(
            ['email' => 'admin@atlasense.com'],
            ['name' => 'Administrator AtlaSense', 'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'user@atlasense.com'],
            ['name' => 'Standard User', 'password' => Hash::make('user123'), 'role' => 'user', 'is_active' => true]
        );

        // 2. Kamus Sentimen Kata Positif & Negatif
        $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'recovery', 'strong', 'success', 'naik', 'tumbuh', 'stabil', 'untung', 'membaik', 'pulih', 'aman'];
        foreach ($positiveWords as $word) {
            KataPositif::firstOrCreate(['word' => $word]);
        }

        $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'conflict', 'shortage', 'perang', 'krisis', 'inflasi', 'terlambat', 'bencana', 'turun', 'konflik', 'kelangkaan'];
        foreach ($negativeWords as $word) {
            KataNegatif::firstOrCreate(['word' => $word]);
        }

        // 3. Pengaturan Bobot Risiko Default (Cuaca 25%, Inflasi 25%, Berita 30%, Kurs 20% = 100%)
        $settings = [
            'risk_weather_weight' => 25,
            'risk_inflation_weight' => 25,
            'risk_news_weight' => 30,
            'risk_currency_weight' => 20,
        ];
        foreach ($settings as $key => $val) {
            PengaturanSistem::updateOrCreate(
                ['key' => $key],
                ['value' => $val, 'type' => 'number', 'description' => 'Bobot perhitungan risiko terbobot AtlaSense']
            );
        }

        // 4. Data Negara Demo (10 Negara Prioritas)
        $countries = [
            ['Indonesia', 'Republic of Indonesia', 'ID', 'IDN', 'Asia', 'South-Eastern Asia', 'Jakarta', 'IDR', 'Indonesian rupiah', 'Indonesian', 'https://flagcdn.com/id.svg', -2.5489, 118.0149, 275501339],
            ['China', "People's Republic of China", 'CN', 'CHN', 'Asia', 'Eastern Asia', 'Beijing', 'CNY', 'Chinese yuan', 'Chinese', 'https://flagcdn.com/cn.svg', 35.8617, 104.1954, 1411750000],
            ['Germany', 'Federal Republic of Germany', 'DE', 'DEU', 'Europe', 'Western Europe', 'Berlin', 'EUR', 'Euro', 'German', 'https://flagcdn.com/de.svg', 51.1657, 10.4515, 83200000],
            ['Australia', 'Commonwealth of Australia', 'AU', 'AUS', 'Oceania', 'Australia and New Zealand', 'Canberra', 'AUD', 'Australian dollar', 'English', 'https://flagcdn.com/au.svg', -25.2744, 133.7751, 26000000],
            ['United States', 'United States of America', 'US', 'USA', 'Americas', 'North America', 'Washington, D.C.', 'USD', 'United States dollar', 'English', 'https://flagcdn.com/us.svg', 37.0902, -95.7129, 333000000],
            ['Japan', 'Japan', 'JP', 'JPN', 'Asia', 'Eastern Asia', 'Tokyo', 'JPY', 'Japanese yen', 'Japanese', 'https://flagcdn.com/jp.svg', 36.2048, 138.2529, 125000000],
            ['Singapore', 'Republic of Singapore', 'SG', 'SGP', 'Asia', 'South-Eastern Asia', 'Singapore', 'SGD', 'Singapore dollar', 'English, Malay', 'https://flagcdn.com/sg.svg', 1.3521, 103.8198, 5637000],
            ['Malaysia', 'Malaysia', 'MY', 'MYS', 'Asia', 'South-Eastern Asia', 'Kuala Lumpur', 'MYR', 'Malaysian ringgit', 'Malay', 'https://flagcdn.com/my.svg', 4.2105, 101.9758, 33900000],
            ['India', 'Republic of India', 'IN', 'IND', 'Asia', 'Southern Asia', 'New Delhi', 'INR', 'Indian rupee', 'Hindi, English', 'https://flagcdn.com/in.svg', 20.5937, 78.9629, 1417000000],
            ['United Kingdom', 'United Kingdom of Great Britain and Northern Ireland', 'GB', 'GBR', 'Europe', 'Northern Europe', 'London', 'GBP', 'Pound sterling', 'English', 'https://flagcdn.com/gb.svg', 55.3781, -3.4360, 67000000],
        ];

        foreach ($countries as $country) {
            Negara::updateOrCreate(
                ['code' => $country[2]],
                [
                    'name' => $country[0],
                    'official_name' => $country[1],
                    'cca3' => $country[3],
                    'region' => $country[4],
                    'subregion' => $country[5],
                    'capital' => $country[6],
                    'currency_code' => $country[7],
                    'currency_name' => $country[8],
                    'language' => $country[9],
                    'flag_url' => $country[10],
                    'latitude' => $country[11],
                    'longitude' => $country[12],
                    'population' => $country[13],
                ]
            );
        }

        // 5. Data Pelabuhan Demo
        $ports = [
            ['Tanjung Priok', 'IDJKT', 'Jakarta', 'Indonesia', -6.1033, 106.8869],
            ['Tanjung Perak', 'IDSUB', 'Surabaya', 'Indonesia', -7.1987, 112.7351],
            ['Belawan', 'IDBLW', 'Medan', 'Indonesia', 3.7850, 98.6940],
            ['Port of Shanghai', 'CNSHA', 'Shanghai', 'China', 31.2304, 121.4737],
            ['Port of Shenzhen', 'CNSZX', 'Shenzhen', 'China', 22.5431, 114.0579],
            ['Port of Hamburg', 'DEHAM', 'Hamburg', 'Germany', 53.5461, 9.9661],
            ['Port Botany', 'AUBTB', 'Sydney', 'Australia', -33.9690, 151.2190],
            ['Port of Los Angeles', 'USLAX', 'Los Angeles', 'United States', 33.7405, -118.2720],
            ['Port of Yokohama', 'JPYOK', 'Yokohama', 'Japan', 35.4437, 139.6380],
            ['Port of Singapore', 'SGSIN', 'Singapore', 'Singapore', 1.2644, 103.8400],
            ['Port Klang', 'MYPKG', 'Klang', 'Malaysia', 3.0000, 101.4000],
            ['Jawaharlal Nehru Port', 'INNSA', 'Navi Mumbai', 'India', 18.9497, 72.9512],
            ['Port of Felixstowe', 'GBFXT', 'Felixstowe', 'United Kingdom', 51.9630, 1.3510],
        ];

        foreach ($ports as $port) {
            $negara = Negara::where('name', $port[3])->first();
            Pelabuhan::updateOrCreate(
                ['unlocode' => $port[1]],
                [
                    'country_id' => $negara?->id,
                    'name' => $port[0],
                    'city' => $port[2],
                    'country_name' => $port[3],
                    'latitude' => $port[4],
                    'longitude' => $port[5],
                    'port_type' => 'Pelabuhan Laut',
                    'status' => 'Aktif',
                    'data_source' => 'Manual'
                ]
            );
        }

        // 6. Artikel Analisis Demo
        Artikel::updateOrCreate(
            ['slug' => 'panduan-membaca-skor-ancaman-atlasense'],
            [
                'user_id' => $admin->id,
                'title' => 'Panduan Membaca Skor Ancaman AtlaSense',
                'excerpt' => 'Panduan komprehensif menginterpretasi empat parameter risiko logistik global.',
                'content' => 'Gunakan skor risiko terbobot (cuaca, inflasi, sentimen berita, dan dampak nilai tukar) sebagai landasan keputusan logistik Anda.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // 7. Riwayat Data Ekonomi Demo (10 Tahun)
        $economicProfiles = [
            'ID' => [932_000_000_000, 0.051, 3.1, 261_000_000],
            'CN' => [11_200_000_000_000, 0.060, 2.0, 1_378_000_000],
            'DE' => [3_500_000_000_000, 0.025, 1.5, 82_000_000],
            'AU' => [1_200_000_000_000, 0.030, 1.8, 24_200_000],
            'US' => [18_700_000_000_000, 0.040, 2.1, 323_000_000],
            'JP' => [5_000_000_000_000, 0.010, 0.5, 127_000_000],
            'SG' => [318_000_000_000, 0.045, 1.2, 5_600_000],
            'MY' => [301_000_000_000, 0.042, 2.0, 31_500_000],
            'IN' => [2_290_000_000_000, 0.065, 4.5, 1_324_000_000],
            'GB' => [2_690_000_000_000, 0.028, 1.9, 65_600_000],
        ];
        $endYear = (int) now()->format('Y') - 1;

        foreach ($economicProfiles as $code => [$baseGdp, $growth, $baseInflation, $basePopulation]) {
            $negara = Negara::where('code', $code)->first();
            if (!$negara) continue;

            for ($offset = 9; $offset >= 0; $offset--) {
                $idx = 9 - $offset;
                $year = $endYear - $offset;
                $gdp = $baseGdp * ((1 + $growth) ** $idx);
                $inflation = max(0.1, $baseInflation + sin($idx * 0.9) * 1.1 + ($idx === 8 ? 2.0 : 0));
                $population = (int) round($basePopulation * ((1.008) ** $idx));

                EkonomiNegara::updateOrCreate(
                    ['country_id' => $negara->id, 'year' => $year],
                    [
                        'gdp' => round($gdp, 2),
                        'inflation' => round($inflation, 4),
                        'exports' => round($gdp * (0.20 + (($idx % 3) * 0.01)), 2),
                        'imports' => round($gdp * (0.17 + (($idx % 2) * 0.011)), 2),
                        'population' => $population,
                    ]
                );
            }
        }

        // 8. Riwayat Kurs Demo (30 Hari)
        $currencyRates = ['IDR' => 16250, 'CNY' => 7.23, 'EUR' => 0.92, 'AUD' => 1.52, 'USD' => 1, 'JPY' => 151.2, 'SGD' => 1.35, 'MYR' => 4.70, 'INR' => 83.4, 'GBP' => 0.79];

        foreach ($currencyRates as $target => $baseRate) {
            $prevRate = null;
            for ($day = 29; $day >= 0; $day--) {
                $date = now()->subDays($day)->toDateString();
                $idx = 29 - $day;
                $rate = $baseRate * (1 + (sin($idx / 3) * 0.005) + ($idx * 0.0002));
                if ($target === 'USD') $rate = 1;
                $change = $prevRate && $prevRate > 0 ? (($rate - $prevRate) / $prevRate) * 100 : 0;
                $recordedAt = $day === 0 ? now() : Carbon::parse($date)->setTime(12, 0);

                KursMataUang::updateOrCreate(
                    ['base_currency' => 'USD', 'target_currency' => $target, 'rate_date' => $date, 'source' => 'Data Demo Seeder'],
                    ['rate' => round($rate, 6), 'change_percent' => round($change, 4), 'recorded_at' => $recordedAt]
                );
                $prevRate = $rate;
            }
        }

        // 9. Data Cuaca Demo
        $weatherProfiles = [
            'ID' => [30.5, 75, 2.5, 70, 15, 25, 80],
            'CN' => [25.0, 58, 0.5, 25, 12, 22, 2],
            'DE' => [17.5, 68, 1.0, 50, 20, 32, 61],
            'AU' => [23.0, 45, 0.0, 5, 24, 35, 1],
            'US' => [25.5, 55, 0.2, 20, 22, 34, 2],
            'JP' => [26.8, 72, 2.8, 72, 18, 29, 63],
            'SG' => [31.2, 78, 4.5, 85, 15, 28, 80],
            'MY' => [29.8, 80, 3.2, 78, 14, 26, 80],
            'IN' => [34.0, 60, 0.6, 35, 20, 31, 3],
            'GB' => [15.8, 75, 1.5, 52, 25, 40, 61],
        ];

        foreach ($weatherProfiles as $code => [$temp, $hum, $rain, $prob, $wind, $gust, $weatherCode]) {
            $negara = Negara::where('code', $code)->first();
            if (!$negara) continue;
            
            $storm = ($wind * .55) + ($gust * .35) + min(20, $rain * 4) + ($prob * .15);

            DataCuaca::updateOrCreate(
                ['country_id' => $negara->id, 'observed_at' => now()->startOfHour()],
                [
                    'temperature' => $temp,
                    'apparent_temperature' => $temp + 1.1,
                    'humidity' => $hum,
                    'precipitation' => $rain,
                    'precipitation_probability' => $prob,
                    'wind_speed' => $wind,
                    'wind_gust' => $gust,
                    'weather_code' => $weatherCode,
                    'condition' => in_array($weatherCode, [61, 63, 65, 80, 81, 82], true) ? 'Hujan' : 'Berawan',
                    'is_day' => true,
                    'storm_risk' => round($storm, 2),
                ]
            );
        }

        // 10. Berita & Analisis Sentimen Detail Demo
        $newsRecords = [
            ['ID', 'Aktivitas Ekspor Logistik Indonesia Tumbuh Stabil', 'Volume logistik pelabuhan meningkat pesat.', 'Positif', 2, 0],
            ['ID', 'Cuaca Buruk Mengancam Delay Pengiriman Kapal', 'Badai petir diprediksi menghambat rute pelayaran laut.', 'Negatif', 0, 2],
            ['SG', 'Trade volume and transit flows remain stable', 'Activity runs normal with zero major interruptions.', 'Netral', 0, 0],
            ['CN', 'Factory logistics improves as export volumes increase', 'Demand bounces back with stronger supply chain performance.', 'Positif', 3, 0],
            ['DE', 'German supply chain hit by transport delays and high inflation', 'Decline in productivity adds threat of logistics recession.', 'Negatif', 0, 2],
            ['AU', 'Port operations steady with zero disruptions', 'Export shipments proceed smoothly.', 'Netral', 0, 0],
        ];

        foreach ($newsRecords as $idx => [$code, $title, $desc, $sent, $pos, $neg]) {
            $negara = Negara::where('code', $code)->first();
            
            $berita = CacheBerita::updateOrCreate(
                ['title' => $title],
                [
                    'country_id' => $negara?->id,
                    'description' => $desc,
                    'source' => 'Intelijen Demo',
                    'published_at' => now()->subHours($idx * 4),
                    'sentiment' => $sent,
                    'positive_score' => $pos,
                    'negative_score' => $neg,
                    'query' => 'demo',
                    'language' => $idx < 2 ? 'id' : 'en',
                ]
            );

            SentimenBerita::updateOrCreate(
                ['news_cache_id' => $berita->id],
                [
                    'sentiment' => $sent,
                    'positive_count' => $pos,
                    'negative_count' => $neg,
                    'neutral_count' => $sent === 'Netral' ? 1 : 0,
                    'matched_positive' => [],
                    'matched_negative' => [],
                ]
            );
        }

        // 11. Riwayat Skor Risiko (7 Hari)
        $riskProfiles = [
            'ID' => [42, 30, 20, 35],
            'CN' => [28, 25, 30, 40],
            'DE' => [35, 18, 15, 30],
            'AU' => [20, 22, 16, 20],
            'US' => [28, 30, 20, 28],
            'JP' => [40, 15, 18, 25],
            'SG' => [38, 16, 12, 22],
            'MY' => [42, 26, 22, 28],
            'IN' => [32, 48, 28, 35],
            'GB' => [36, 38, 18, 32],
        ];

        foreach ($riskProfiles as $code => $scores) {
            $negara = Negara::where('code', $code)->first();
            if (!$negara) continue;

            for ($day = 6; $day >= 0; $day--) {
                [$weather, $inflation, $news, $currency] = array_map(
                    fn ($score) => max(0, min(100, $score + sin($day + $negara->id) * 3)),
                    $scores
                );
                
                // Algoritma kustom 25/25/30/20 AtlaSense
                $total = round(($weather * .25) + ($inflation * .25) + ($news * .30) + ($currency * .20), 2);
                $level = $total <= 30 ? 'Rendah' : ($total <= 60 ? 'Sedang' : 'Tinggi');
                $calculatedAt = now()->subDays($day)->startOfDay()->addHours(9);

                $skor = SkorRisiko::updateOrCreate(
                    ['country_id' => $negara->id, 'calculated_at' => $calculatedAt],
                    [
                        'weather_score' => round($weather, 2),
                        'inflation_score' => round($inflation, 2),
                        'currency_score' => round($currency, 2),
                        'news_score' => round($news, 2),
                        'total_score' => $total,
                        'risk_level' => $level,
                    ]
                );

                $compScores = ['Cuaca' => $weather, 'Inflasi' => $inflation, 'Berita' => $news, 'Mata Uang' => $currency];
                $weights = ['Cuaca' => 25, 'Inflasi' => 25, 'Berita' => 30, 'Mata Uang' => 20];
                
                foreach ($compScores as $comp => $val) {
                    $skor->components()->updateOrCreate(
                        ['component' => $comp],
                        [
                            'raw_value' => $val,
                            'normalized_score' => round($val, 2),
                            'weight' => $weights[$comp],
                            'weighted_score' => round($val * ($weights[$comp] / 100), 2),
                            'notes' => 'Simulasi data seeder demo historis.',
                        ]
                    );
                }
            }
        }
    }
}
