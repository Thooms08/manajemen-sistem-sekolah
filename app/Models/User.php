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
        'role',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRoleAttribute(): string
    {
        return $this->attributes['role'] ?? $this->attributes['rules'] ?? 'guest';
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = $value;
    }

    public function getRulesAttribute(): string
    {
        return $this->attributes['role'] ?? $this->attributes['rules'] ?? 'guest';
    }

    public function setRulesAttribute($value): void
    {
        $this->attributes['role'] = $value;
    }

    // Otomatis menghash password saat disimpan (Laravel 10/11/12 Style)
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}