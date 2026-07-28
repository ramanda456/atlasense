<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LayananNegara;

class SinkronNegara extends Command
{
    protected $signature = 'atlasense:sinkron-negara';

    protected $description = 'Sinkronkan daftar 250 negara dari REST Countries API ke database';

    public function handle(LayananNegara $layanan): int
    {
        $this->info('Memulai sinkronisasi data negara...');
        $jumlah = $layanan->sinkronNegara();

        if ($jumlah > 0) {
            $this->info("Berhasil! {$jumlah} negara telah disinkronkan ke database.");
            return self::SUCCESS;
        }

        $this->error('Sinkronisasi data negara gagal.');
        return self::FAILURE;
    }
}
