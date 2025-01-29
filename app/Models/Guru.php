<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama',
        'jenis_kelamin',
        'no_hp',
        'image_profile',
        'alamat'
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/assets/guru/' . $image)
        );
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'username', 'nip');
    }
}
