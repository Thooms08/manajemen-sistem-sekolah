<?php

namespace App\Models\Dokumen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dokumen extends Model
{
    use HasFactory;

    // Arahkan ke koneksi database kedua
    protected $connection = 'dokumen_db';
    
    protected $table = 'dokumens';
    
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        
        // Generate UUID dimasukkan ke kolom 'uuid', bukan ke kolom 'id'
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relasi untuk Parent dan Children Folder
    public function children()
    {
        // Tetap pakai parent_id, otomatis nge-relasiin ke primary key 'id'
        return $this->hasMany(Dokumen::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Dokumen::class, 'parent_id', 'id');
    }
}