<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Negara;
use App\Services\LayananNegara;

class SinkronEkonomi extends Command
{
    protected $signature = 'atlasense:sinkron-ekonomi {country? : Kode ISO2 negara} {--years=10 : Jumlah tahun data historis} {--all : Sinkronkan untuk seluruh negara di database}';

    protected $description = 'Sinkronkan data historis ekonomi (GDP, Inflasi, Populasi, Impor, Ekspor) dari World Bank API';

    public function handle(LayananNegara $layanan): int
    {
        $query = Negara::query();
        
        if ($code = $this->argument('country')) {
            $query->where('code', strtoupper($code));
        } elseif (!$this->option('all')) {
            // Default sinkronisasi ke beberapa negara prioritas saja jika opsi --all tidak diset
            $query->whereIn('code', ['ID', 'CN', 'DE', 'AU', 'US', 'JP', 'SG', 'MY', 'IN', 'GB']);
        }

        $negaraList = $query->orderBy('name')->get();
        if ($negaraList->isEmpty()) {
            $this->error('Negara tidak ditemukan di database.');
            return self::FAILURE;
        }

        $years = (int) $this->option('years');
        $this->info("Memulai sinkronisasi data ekonomi untuk {$negaraList->count()} negara selama {$years} tahun terakhir...");
        
        $bar = $this->output->createProgressBar($negaraList->count());
        $bar->start();

        foreach ($negaraList as $negara) {
            try {
                $layanan->riwayatEkonomi($negara, $years, true);
            } catch (\Throwable $e) {
                $this->error("\nGagal memproses {$negara->name}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info('Riwayat ekonomi berhasil disinkronkan dari World Bank API.');
        return self::SUCCESS;
    }
}
