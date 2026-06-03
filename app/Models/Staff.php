<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $staff = ['nama_staff','jabatan','email','no_wa','alamat'];
}
