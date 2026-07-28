<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'name', 'official_name', 'code', 'cca3', 'region', 'subregion',
        'capital', 'currency_code', 'currency_name', 'language', 'flag_url',
        'latitude', 'longitude', 'population'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'population' => 'integer',
    ];

    public function economics()
    {
        return $this->hasMany(EkonomiNegara::class, 'country_id');
    }

    public function latestEconomic()
    {
        return $this->hasOne(EkonomiNegara::class, 'country_id')->latestOfMany('year');
    }

    public function risks()
    {
        return $this->hasMany(SkorRisiko::class, 'country_id');
    }

    public function latestRisk()
    {
        return $this->hasOne(SkorRisiko::class, 'country_id')->latestOfMany();
    }

    public function ports()
    {
        return $this->hasMany(Pelabuhan::class, 'country_id');
    }

    public function weatherHistory()
    {
        return $this->hasMany(DataCuaca::class, 'country_id');
    }

    public function latestWeather()
    {
        return $this->hasOne(DataCuaca::class, 'country_id')->latestOfMany('observed_at');
    }
}
