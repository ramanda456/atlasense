<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Negara;
use App\Models\PengaturanSistem;
use App\Services\PenganalisaBerita;

class SinkronBerita extends Command
{
    protected $signature = 'atlasense:sinkron-berita 
                            {--limit=80 : Jumlah negara per proses}
                            {--articles=5 : Jumlah artikel per negara}
                            {--reset : Reset sinkronisasi dari negara pertama}';

    protected $description = 'Sinkronkan berita logistik & rantai pasok global dari GNews API secara bertahap';

    public function handle(PenganalisaBerita $penganalisa): int
    {
        if (blank(config('services.gnews.key'))) {
            $this->error('GNEWS_API_KEY belum dikonfigurasi di file .env.');
            return self::FAILURE;
        }

        $limit = max(1, min((int) $this->option('limit'), 250));
        $articles = max(1, min((int) $this->option('articles'), 10));
        $keyPengaturan = 'news_sync_last_country_id';

        if ($this->option('reset')) {
            PengaturanSistem::updateOrCreate(
                ['key' => $keyPengaturan],
                ['value' => '0', 'type' => 'integer', 'description' => 'ID negara terakhir sinkronisasi berita']
            );
        }

        $lastId = (int) (PengaturanSistem::where('key', $keyPengaturan)->value('value') ?? 0);

        $negaraList = Negara::query()
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        // Mulai dari awal jika sudah mencapai negara terakhir
        if ($negaraList->isEmpty()) {
            $lastId = 0;
            $negaraList = Negara::query()
                ->orderBy('id')
                ->limit($limit)
                ->get();
        }

        if ($negaraList->isEmpty()) {
            $this->warn('Belum ada data negara di database. Harap jalankan `php artisan atlasense:sinkron-negara` terlebih dahulu.');
            return self::SUCCESS;
        }

        $queryCari = 'logistics OR trade OR shipping OR economy OR inflation OR export OR import';
        $this->info("Memulai sinkronisasi berita GNews untuk {$negaraList->count()} negara...");
        
        $bar = $this->output->createProgressBar($negaraList->count());
        $bar->start();

        foreach ($negaraList as $negara) {
            try {
                $penganalisa->ambilBerita(
                    negara: $negara,
                    query: $queryCari,
                    limit: $articles,
                    paksa: true
                );

                PengaturanSistem::updateOrCreate(
                    ['key' => $keyPengaturan],
                    ['value' => (string) $negara->id, 'type' => 'integer', 'description' => 'ID negara terakhir sinkronisasi berita']
                );
            } catch (\Throwable $e) {
                $this->warn("\nGagal mengambil berita untuk {$negara->name}: " . $e->getMessage());
            }

            $bar->advance();

            // GNews API Free tier rate limits: beri jeda 2 detik per request agar aman
            sleep(2);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sinkronisasi berita selesai. Negara terakhir diproses: " . $negaraList->last()->name);
        return self::SUCCESS;
    }
}
