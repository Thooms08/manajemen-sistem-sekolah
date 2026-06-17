<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Model;

class PpdbFormSetting extends Model
{
    protected $fillable = [
        'field_name',
        'field_label',
        'field_category',
        'is_active',
        'is_required',
        'sort_order',
    ];
}
