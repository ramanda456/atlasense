<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchImporPelabuhan extends Model
{
    protected $table = 'port_import_batches';

    protected $fillable = [
        'user_id', 'filename', 'source', 'total_rows', 'imported_rows', 'skipped_rows', 'notes'
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'imported_rows' => 'integer',
        'skipped_rows' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
