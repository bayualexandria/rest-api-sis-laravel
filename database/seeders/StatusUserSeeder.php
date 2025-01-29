<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status_user')->insert(['status' => 'Admin']);
        DB::table('status_user')->insert(['status' => 'Guru']);
        DB::table('status_user')->insert(['status' => 'Siswa']);
    }
}
