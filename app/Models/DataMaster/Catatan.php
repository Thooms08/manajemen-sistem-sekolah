<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class Catatan extends Model
{
    protected $table = 'catatan';

    protected $fillable = [
        'uuid',
        'id_user',
        'label',
        'catatan',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
