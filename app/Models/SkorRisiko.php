<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkorRisiko extends Model
{
    protected $table = 'risk_scores';

    protected $fillable = [
        'country_id', 'weather_score', 'inflation_score', 'currency_score', 'news_score', 'total_score', 'risk_level', 'calculated_at'
    ];

    protected $casts = [
        'weather_score' => 'float',
        'inflation_score' => 'float',
        'currency_score' => 'float',
        'news_score' => 'float',
        'total_score' => 'float',
        'calculated_at' => 'datetime',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'country_id');
    }

    public function components()
    {
        return $this->hasMany(KomponenRisiko::class, 'risk_score_id');
    }
}
