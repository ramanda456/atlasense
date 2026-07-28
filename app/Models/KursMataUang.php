<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KursMataUang extends Model
{
    protected $table = 'currency_rates';

    protected $fillable = [
        'base_currency', 'target_currency', 'rate', 'change_percent', 'rate_date', 'source', 'recorded_at'
    ];

    protected $casts = [
        'rate' => 'float',
        'change_percent' => 'float',
        'rate_date' => 'date',
        'recorded_at' => 'datetime',
    ];
}
