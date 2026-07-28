<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogApi extends Model
{
    protected $table = 'api_logs';

    protected $fillable = [
        'service', 'endpoint', 'method', 'status_code', 'response_time_ms', 'success', 'message', 'requested_at'
    ];

    protected $casts = [
        'success' => 'boolean',
        'response_time_ms' => 'integer',
        'requested_at' => 'datetime',
    ];
}
