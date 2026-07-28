<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KataPositif extends Model
{
    protected $table = 'positive_words';

    protected $fillable = ['word'];
}
