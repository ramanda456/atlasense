<?php

namespace App\Services;

use App\Models\LogApi;

class PelacakApi
{
    /**
     * Mencatat log pemanggilan API eksternal ke database
     */
    public static function catat(string $layanan, string $endpoint, int $status, bool $sukses, float $mulai, ?string $pesan = null): void
    {
        try {
            $waktuRespons = (int) ((microtime(true) - $mulai) * 1000);

            LogApi::create([
                'service' => $layanan,
                'endpoint' => $endpoint,
                'method' => 'GET',
                'status_code' => $status,
                'response_time_ms' => $waktuRespons,
                'success' => $sukses,
                'message' => $pesan,
                'requested_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Abaikan jika database error agar tidak mengganggu jalannya aplikasi utama
        }
    }
}
