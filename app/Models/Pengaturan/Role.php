<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'uuid',
        'slug',
        'nama',
        'deskripsi',
        'warna',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Gunakan UUID sebagai route key agar URL tidak mengekspos ID numerik.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Auto-generate UUID saat create jika belum ada.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }

    /**
     * Cek apakah role ini adalah role Admin yang dilindungi.
     */
    public function isAdmin(): bool
    {
        return $this->slug === 'admin';
    }

    public function getPermissionFor(string $modul): array
    {
        $perm = $this->permissions->firstWhere('modul', $modul);
        return $perm ? ($perm->aksi ?? []) : [];
    }

    public function can(string $modul, string $aksi): bool
    {
        return in_array($aksi, $this->getPermissionFor($modul));
    }
}
