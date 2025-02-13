<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSekolahSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('table_profile_sekolah')->insert([
            'nama_sekolah' => 'SMK XXX XXXX',
            'no_telp' => 'XXXXXXXX',
            'alamat_sekolah' => 'XXXXX XXXXX XXXXX',
            'akreditasi' => 'B',
            'image'
            => 'assets/images/users.png',
        ]);
    }
}
