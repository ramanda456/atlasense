<?php

namespace App\Services;

use App\Models\KataPositif;
use App\Models\KataNegatif;

class MesinSentimen
{
    /**
     * Menganalisis sentimen teks menggunakan kamus kata positif & negatif dari database
     */
    public function analisis(string $teks): array
    {
        // Bersihkan teks dan split menjadi array kata-kata
        $bersih = strtolower(preg_replace('/[^a-zA-ZÀ-ÿ0-9\s]/u', ' ', $teks));
        $kataTeks = array_values(array_filter(preg_split('/\s+/u', $bersih)));

        // Ambil kamus kata positif & negatif dari database
        $positif = KataPositif::pluck('word')->map(fn ($w) => strtolower($w))->all();
        $negatif = KataNegatif::pluck('word')->map(fn ($w) => strtolower($w))->all();

        // Cari kecocokan kata
        $matchedP = array_values(array_intersect($kataTeks, $positif));
        $matchedN = array_values(array_intersect($kataTeks, $negatif));

        $pCount = count($matchedP);
        $nCount = count($matchedN);

        $sentimen = 'Netral';
        if ($pCount > $nCount) {
            $sentimen = 'Positif';
        } elseif ($nCount > $pCount) {
            $sentimen = 'Negatif';
        }

        return [
            'sentiment' => $sentimen,
            'positive' => $pCount,
            'negative' => $nCount,
            'matched_positive' => array_values(array_unique($matchedP)),
            'matched_negative' => array_values(array_unique($matchedN))
        ];
    }
}
