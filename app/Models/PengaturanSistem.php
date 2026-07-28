<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'key', 'value', 'type', 'description'
    ];
}
