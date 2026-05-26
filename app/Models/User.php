<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
        'rules',
    ];

    protected $hidden = [
        'password',
    ];

    // Otomatis menghash password saat disimpan (Laravel 10/11/12 Style)
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}