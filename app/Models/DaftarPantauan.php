<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarPantauan extends Model
{
    protected $table = 'watchlists';

    protected $fillable = [
        'user_id', 'country_id', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'country_id');
    }
}
