<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Negara;
use App\Services\PenganalisaKurs;

class SinkronKurs extends Command
{
    protected $signature = 'atlasense:sinkron-kurs {target=IDR : Target mata uang} {--base=USD : Base mata uang} {--days=30 : Jumlah hari data historis} {--all : Ambil semua mata uang negara yang ada di DB}';

    protected $description = 'Sinkronkan nilai tukar realtime dan riwayat kurs untuk visualisasi grafik tren';

    public function handle(PenganalisaKurs $penganalisa): int
    {
        $base = $this->option('base');
        $days = (int) $this->option('days');
        
        $targets = $this->option('all')
            ? Negara::whereNotNull('currency_code')->distinct()->pluck('currency_code')->filter()->unique()
            : collect([strtoupper($this->argument('target'))]);

        $this->info("Memulai sinkronisasi kurs {$base} untuk {$targets->count()} mata uang target...");

        foreach ($targets as $target) {
            $this->line("Memproses {$base}/{$target}...");
            try {
                $penganalisa->histori($base, $target, $days, true);
            } catch (\Throwable $e) {
                $this->error("Gagal sinkronisasi {$base}/{$target}: " . $e->getMessage());
            }
        }

        $this->info('Sinkronisasi kurs mata uang selesai.');
        return self::SUCCESS;
    }
}
