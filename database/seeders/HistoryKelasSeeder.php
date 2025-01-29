<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('history_kelas')->insert(['kelas_id' => 1, 'wali_kelas' => 1, 'semester_id' => 1]);
        DB::table('history_kelas')->insert(['kelas_id' => 4, 'wali_kelas' => 1, 'semester_id' => 1]);
        DB::table('history_kelas')->insert(['kelas_id' => 7, 'wali_kelas' => 1, 'semester_id' => 1]);
    }
}
