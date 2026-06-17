<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $fillable = [
        'role_id',
        'modul',
        'aksi',
    ];

    protected $casts = [
        'aksi' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
