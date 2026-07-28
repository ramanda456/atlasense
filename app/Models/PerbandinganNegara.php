<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerbandinganNegara extends Model
{
    protected $table = 'country_comparisons';

    protected $fillable = [
        'user_id', 'country_a_id', 'country_b_id', 'result'
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function negaraA()
    {
        return $this->belongsTo(Negara::class, 'country_a_id');
    }

    public function negaraB()
    {
        return $this->belongsTo(Negara::class, 'country_b_id');
    }
}
