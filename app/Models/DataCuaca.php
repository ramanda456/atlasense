<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataCuaca extends Model
{
    protected $table = 'weather_data';

    protected $fillable = [
        'country_id', 'temperature', 'apparent_temperature', 'humidity', 'precipitation',
        'precipitation_probability', 'wind_speed', 'wind_gust', 'weather_code', 'condition',
        'is_day', 'storm_risk', 'observed_at'
    ];

    protected $casts = [
        'temperature' => 'float',
        'apparent_temperature' => 'float',
        'humidity' => 'float',
        'precipitation' => 'float',
        'precipitation_probability' => 'float',
        'wind_speed' => 'float',
        'wind_gust' => 'float',
        'weather_code' => 'integer',
        'is_day' => 'boolean',
        'storm_risk' => 'float',
        'observed_at' => 'datetime',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'country_id');
    }
}
