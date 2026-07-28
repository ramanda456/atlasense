<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimenBerita extends Model
{
    protected $table = 'news_sentiments';

    protected $fillable = [
        'news_cache_id', 'sentiment', 'positive_count', 'negative_count', 'neutral_count', 'matched_positive', 'matched_negative'
    ];

    protected $casts = [
        'positive_count' => 'integer',
        'negative_count' => 'integer',
        'neutral_count' => 'integer',
        'matched_positive' => 'array',
        'matched_negative' => 'array',
    ];

    public function berita()
    {
        return $this->belongsTo(CacheBerita::class, 'news_cache_id');
    }
}
