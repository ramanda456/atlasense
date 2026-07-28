<?php

namespace App\Services;

use App\Models\Negara;
use App\Models\SkorRisiko;
use App\Models\PengaturanSistem;
use Illuminate\Support\Facades\DB;

class PenilaiAncaman
{
    public function __construct(
        private LayananNegara $layananNegara,
        private PenganalisaCuaca $penganalisaCuaca,
        private PenganalisaKurs $penganalisaKurs,
        private PenganalisaBerita $penganalisaBerita
    ) {}

    /**
     * Menghitung nilai risiko (skor risiko) terbobot untuk suatu negara
     */
    public function hitung(Negara $negara): SkorRisiko
    {
        // 1. Ekstrak kode mata uang 3-huruf yang valid (misal 'EUR' dari 'EUR, USD')
        $cleanCurrency = preg_match('/[A-Z]{3}/i', $negara->currency_code ?? '', $m) ? strtoupper($m[0]) : null;

        $ekonomi = null;
        try {
            $ekonomi = $this->layananNegara->riwayatEkonomi($negara, 10)->sortByDesc('year')->first();
        } catch (\Throwable $e) {}

        $cuaca = null;
        try {
            $cuaca = $this->penganalisaCuaca->saatIni($negara);
        } catch (\Throwable $e) {}

        $kurs = null;
        if ($cleanCurrency && $cleanCurrency !== 'USD') {
            try {
                $kurs = $this->penganalisaKurs->ambilKurs('USD', $cleanCurrency);
            } catch (\Throwable $e) {}
        }
        
        $beritaList = collect();
        try {
            $beritaList = $this->penganalisaBerita->ambilBerita($negara, 'logistics trade shipping economy geopolitics', 10);
        } catch (\Throwable $e) {}

        // 2. Normalisasi skor tiap indikator (skala 0 - 100)
        $skorCuaca = min(100, max(0, (float) ($cuaca?->storm_risk ?? 20)));
        
        $inflasi = (float) ($ekonomi?->inflation ?? 5);
        $skorInflasi = min(100, max(0, $inflasi * 8)); // Normalisasi inflasi ke 0-100
        
        $perubahanKurs = abs((float) ($kurs?->change_percent ?? 0));
        $skorKurs = min(100, $perubahanKurs * 20); // Normalisasi perubahan kurs ke 0-100
        
        $jumlahNegatif = $beritaList->where('sentiment', 'Negatif')->count();
        $skorBerita = $beritaList->count() ? ($jumlahNegatif / $beritaList->count()) * 100 : 25;

        // 3. Ambil bobot risiko dari PengaturanSistem (fallback ke default kustom kita 25/25/30/20)
        $bobot = [
            'Cuaca' => (float) PengaturanSistem::where('key', 'risk_weather_weight')->value('value') ?: 25,
            'Inflasi' => (float) PengaturanSistem::where('key', 'risk_inflation_weight')->value('value') ?: 25,
            'Berita' => (float) PengaturanSistem::where('key', 'risk_news_weight')->value('value') ?: 30,
            'Mata Uang' => (float) PengaturanSistem::where('key', 'risk_currency_weight')->value('value') ?: 20,
        ];

        // Pastikan total bobot bernilai 100%
        $totalBobot = array_sum($bobot) ?: 100;
        $bobot = array_map(fn ($b) => ($b / $totalBobot) * 100, $bobot);

        // Hitung total skor risiko terbobot
        $totalSkor = round(
            $skorCuaca * ($bobot['Cuaca'] / 100)
            + $skorInflasi * ($bobot['Inflasi'] / 100)
            + $skorBerita * ($bobot['Berita'] / 100)
            + $skorKurs * ($bobot['Mata Uang'] / 100),
            2
        );

        // Kategori tingkat risiko
        $tingkat = 'Rendah';
        if ($totalSkor > 60) {
            $tingkat = 'Tinggi';
        } elseif ($totalSkor > 30) {
            $tingkat = 'Sedang';
        }

        // 4. Simpan ke database
        return DB::transaction(function () use (
            $negara, $skorCuaca, $skorInflasi, $skorKurs, $skorBerita,
            $totalSkor, $tingkat, $bobot, $cuaca, $inflasi, $perubahanKurs, $jumlahNegatif, $beritaList
        ) {
            $skor = SkorRisiko::create([
                'country_id' => $negara->id,
                'weather_score' => round($skorCuaca, 2),
                'inflation_score' => round($skorInflasi, 2),
                'currency_score' => round($skorKurs, 2),
                'news_score' => round($skorBerita, 2),
                'total_score' => $totalSkor,
                'risk_level' => $tingkat,
                'calculated_at' => now(),
            ]);

            // Detail komponen risiko
            $komponen = [
                ['Cuaca', $cuaca?->storm_risk, $skorCuaca, $bobot['Cuaca'], 'Risiko badai, hembusan angin, curah hujan, & kelembapan.'],
                ['Inflasi', $inflasi, $skorInflasi, $bobot['Inflasi'], 'Tingkat inflasi tahunan rier yang dinormalisasi.'],
                ['Berita', $jumlahNegatif, $skorBerita, $bobot['Berita'], $jumlahNegatif . ' berita logistik negatif dari ' . $beritaList->count() . ' berita.'],
                ['Mata Uang', $perubahanKurs, $skorKurs, $bobot['Mata Uang'], 'Persentase perubahan nilai tukar mata uang lokal terhadap USD.'],
            ];

            foreach ($komponen as [$nama, $raw, $normal, $weight, $notes]) {
                $skor->components()->create([
                    'component' => $nama,
                    'raw_value' => $raw,
                    'normalized_score' => round($normal, 2),
                    'weight' => round($weight, 2),
                    'weighted_score' => round($normal * ($weight / 100), 2),
                    'notes' => $notes,
                ]);
            }

            return $skor->load(['negara', 'components']);
        });
    }
}
