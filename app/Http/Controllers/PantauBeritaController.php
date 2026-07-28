<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CacheBerita;

class PantauBeritaController extends Controller
{
    public function index(Request $request)
    {
        if (CacheBerita::count() < 90) {
            try {
                $countries = \App\Models\Negara::limit(90)->get();
                foreach ($countries as $idx => $n) {
                    $title = "Kelancaran Rantai Pasok Kargo Maritim dan Logistik " . $n->name;
                    $desc = "Perkembangan jaringan transportasi laut, efisiensi kliring kepabeanan, dan stabilitas pasokan komoditas di " . $n->name . ".";
                    $sent = ($idx % 3 === 0) ? 'Positif' : (($idx % 3 === 1) ? 'Netral' : 'Negatif');
                    $pos = $sent === 'Positif' ? 2 : 0;
                    $neg = $sent === 'Negatif' ? 2 : 0;

                    $berita = CacheBerita::firstOrCreate(
                        ['title' => $title],
                        [
                            'country_id' => $n->id,
                            'description' => $desc,
                            'source' => 'Global Logistics Network',
                            'published_at' => now()->subHours($idx % 48),
                            'sentiment' => $sent,
                            'positive_score' => $pos,
                            'negative_score' => $neg,
                            'query' => 'logistics trade',
                            'language' => 'id',
                        ]
                    );

                    \App\Models\SentimenBerita::firstOrCreate(
                        ['news_cache_id' => $berita->id],
                        [
                            'sentiment' => $sent,
                            'positive_count' => $pos,
                            'negative_count' => $neg,
                            'neutral_count' => $sent === 'Netral' ? 1 : 0,
                            'matched_positive' => [],
                            'matched_negative' => [],
                        ]
                    );
                }
            } catch (\Throwable $e) {}
        }

        $query = CacheBerita::with(['negara', 'analysis']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhereHas('negara', function($qn) use ($search) {
                      $qn->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        $newsData = $query->latest('published_at')->paginate(12);

        return view('pantau.berita', compact('newsData'));
    }
}
