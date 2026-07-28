<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User — Pengguna platform AtlaSense
 * Menggunakan Authenticatable bawaan Laravel untuk mendukung Auth::attempt()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom yang boleh diisi secara massal (termasuk 'role' yang baru)
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: User memiliki banyak watchlist (daftar pantauan)
     */
    public function watchlists()
    {
        return $this->hasMany(DaftarPantauan::class, 'user_id');
    }

    /**
     * Cek apakah user adalah administrator
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
