<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkonomiNegara extends Model
{
    protected $table = 'country_economics';

    protected $fillable = [
        'country_id', 'year', 'gdp', 'inflation', 'exports', 'imports', 'population'
    ];

    protected $casts = [
        'year' => 'integer',
        'gdp' => 'float',
        'inflation' => 'float',
        'exports' => 'float',
        'imports' => 'float',
        'population' => 'integer',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'country_id');
    }
}
