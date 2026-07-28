<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelabuhan extends Model
{
    protected $table = 'ports';

    protected $fillable = [
        'country_id', 'name', 'unlocode', 'wpi_number', 'city', 'country_name',
        'latitude', 'longitude', 'port_type', 'harbor_size', 'harbor_type',
        'status', 'data_source', 'imported_at'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'imported_at' => 'datetime',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'country_id');
    }
}
