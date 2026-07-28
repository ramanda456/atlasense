<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PenganalisaCuaca;

class SinkronCuaca extends Command
{
    protected $signature = 'atlasense:sinkron-cuaca {--limit=250 : Jumlah negara yang disinkronkan}';

    protected $description = 'Sinkronkan data cuaca terkini dari Open-Meteo API';

    public function handle(PenganalisaCuaca $penganalisa): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Memulai sinkronisasi cuaca terkini untuk maksimal {$limit} negara...");
        
        $data = $penganalisa->sinkronIkhtisar($limit);
        
        $this->info("Berhasil! Cuaca untuk {$data->count()} negara sekarang tersedia di sistem.");
        return self::SUCCESS;
    }
}
