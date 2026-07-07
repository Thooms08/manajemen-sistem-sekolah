<?php

namespace App\Models\Informasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileSekolah extends Model
{
    protected $table = 'profile_sekolah';

    protected $fillable = [
        'nama_sekolah',
        'nis',
        'logo',
        'foto_sekolah',
        'deskripsi',
        'alamat',
        'tautan_google_maps',
        'no_hp',
        'email',
        'akreditasi',
    ];

    /**
     * Gunakan UUID sebagai route key sehingga URL tidak mengekspos ID numerik.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Auto-generate UUID saat record baru dibuat.
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
}
