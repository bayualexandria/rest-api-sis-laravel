<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileSekolah extends Model
{
    use HasFactory;

    protected $table = 'table_profile_sekolah';
    protected $fillable = [
        'nama_sekolah',
        'no_telp',
        'akreditasi',
        'alamat_sekolah',

    ];
}
