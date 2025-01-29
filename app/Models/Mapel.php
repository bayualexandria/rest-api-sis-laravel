<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mapel';
    protected $fillable = [
        'nama_mapel', 'kelas_id', 'guru_id', 'hari', 'date_start', 'date_end'
    ];
}
