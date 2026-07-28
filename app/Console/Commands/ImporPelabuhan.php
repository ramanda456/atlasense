<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PengelolaPelabuhan;

class ImporPelabuhan extends Command
{
    protected $signature = 'atlasense:impor-pelabuhan {file=database/data/world_port_index_sample.csv : File CSV yang akan diimpor}';

    protected $description = 'Impor data CSV World Port Index ke tabel ports';

    public function handle(PengelolaPelabuhan $pengelola): int
    {
        $file = base_path($this->argument('file'));
        
        if (!is_file($file)) {
            $file = $this->argument('file');
        }
        
        if (!is_file($file)) {
            $this->error('File tidak ditemukan: ' . $this->argument('file'));
            return self::FAILURE;
        }

        $this->info('Memulai impor pelabuhan dari CSV...');
        
        try {
            $batch = $pengelola->impor($file);
            $this->info("Impor selesai! Batch ID: {$batch->id}. Total baris diproses: {$batch->total_rows}. Terimpor: {$batch->imported_rows}. Dilewati: {$batch->skipped_rows}.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal mengimpor pelabuhan: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
