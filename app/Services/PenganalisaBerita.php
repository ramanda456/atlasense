<?php

namespace App\Services;

use App\Models\Negara;
use App\Models\CacheBerita;
use App\Models\SentimenBerita;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class PenganalisaBerita
{
    public function __construct(
        private MesinSentimen $mesinSentimen
    ) {}

    /**
     * Mengambil berita logistik untuk suatu negara menggunakan GNews API
     */
    public function ambilBerita(
        ?Negara $negara = null,
        string $query = 'logistics OR trade OR shipping OR economy OR inflation OR export OR import',
        int $limit = 10,
        bool $paksa = false
    ): Collection {
        // GNews API Free tier max 10 articles per request
        $limit = max(1, min($limit, 10));

        // Hindari pemanggilan API berulang jika cache masih baru (< 12 jam)
        if (!$paksa && $this->adaCacheBaru($negara)) {
            return $this->cached($negara, $limit);
        }

        $apiKey = config('services.gnews.key');

        // Jika API Key kosong, tampilkan saja data tersimpan dari DB
        if (blank($apiKey)) {
            return $this->cached($negara, $limit);
        }

        $baseUrl = rtrim(config('services.gnews.url', 'https://gnews.io/api/v4'), '/');
        $url = $baseUrl . '/search';
        $query = trim($query);

        if ($query === '') {
            $query = 'logistics OR trade OR shipping OR economy OR inflation OR export OR import';
        }

        // Susun query pencarian
        if ($negara) {
            $countryName = str_replace('"', '', $negara->name);
            $search = sprintf('"%s" AND (%s)', $countryName, $query);
        } else {
            $search = '(' . $query . ')';
        }

        $params = [
            'q' => $search,
            'lang' => 'en',
            'max' => $limit,
            'in' => 'title,description',
            'sortby' => 'publishedAt',
            'apikey' => $apiKey,
        ];

        $mulai = microtime(true);

        try {
            $response = Http::timeout(30)->retry(2, 500)->acceptJson()->get($url, $params);
            PelacakApi::catat('GNews', $url . '?q=' . urlencode($search), $response->status(), $response->successful(), $mulai);
            $response->throw();

            $articles = $response->json('articles', []);

            foreach ($articles as $article) {
                $title = trim($article['title'] ?? 'Tanpa Judul');
                $description = trim($article['description'] ?? '');

                // Analisis sentimen berita menggunakan Lexicon-Based PHP
                $analisis = $this->mesinSentimen->analisis($title . ' ' . $description);
                $articleUrl = $article['url'] ?? null;

                // URL unik sebagai identitas agar tidak duplikat
                $identitas = filled($articleUrl)
                    ? ['url' => $articleUrl]
                    : ['title' => $title, 'country_id' => $negara?->id];

                $berita = CacheBerita::updateOrCreate(
                    $identitas,
                    [
                        'country_id' => $negara?->id,
                        'title' => $title,
                        'description' => $description ?: null,
                        'url' => $articleUrl,
                        'image_url' => $article['image'] ?? null,
                        'source' => $article['source']['name'] ?? 'GNews',
                        'published_at' => isset($article['publishedAt']) ? \Carbon\Carbon::parse($article['publishedAt']) : now(),
                        'sentiment' => $analisis['sentiment'],
                        'positive_score' => $analisis['positive'],
                        'negative_score' => $analisis['negative'],
                        'query' => $search,
                        'language' => $article['lang'] ?? 'en',
                    ]
                );

                SentimenBerita::updateOrCreate(
                    ['news_cache_id' => $berita->id],
                    [
                        'sentiment' => $analisis['sentiment'],
                        'positive_count' => $analisis['positive'],
                        'negative_count' => $analisis['negative'],
                        'neutral_count' => ($analisis['positive'] === 0 && $analisis['negative'] === 0) ? 1 : 0,
                        'matched_positive' => $analisis['matched_positive'],
                        'matched_negative' => $analisis['matched_negative'],
                    ]
                );
            }
        } catch (\Throwable $e) {
            PelacakApi::catat('GNews', $url, 0, false, $mulai, $e->getMessage());
        }

        return $this->cached($negara, $limit);
    }

    /**
     * Memeriksa apakah berita negara sudah terupdate < 12 jam
     */
    private function adaCacheBaru(?Negara $negara): bool
    {
        return CacheBerita::query()
            ->when(
                $negara,
                fn ($query) => $query->where('country_id', $negara->id),
                fn ($query) => $query->whereNull('country_id')
            )
            ->where('updated_at', '>=', now()->subHours(12))
            ->exists();
    }

    /**
     * Mengambil data berita tersimpan di DB
     */
    private function cached(?Negara $negara, int $limit): Collection
    {
        return CacheBerita::with(['negara', 'analysis'])
            ->when(
                $negara,
                fn ($query) => $query->where('country_id', $negara->id),
                fn ($query) => $query->whereNull('country_id')
            )
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}
