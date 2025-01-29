<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kelas')->insert(['nama_kelas' => 'X', 'jurusan' => 'Administrasi Perkantoran']);
        DB::table('kelas')->insert(['nama_kelas' => 'XI', 'jurusan' => 'Administrasi Perkantoran']);
        DB::table('kelas')->insert(['nama_kelas' => 'XII', 'jurusan' => 'Administrasi Perkantoran']);

        DB::table('kelas')->insert(['nama_kelas' => 'X', 'jurusan' => 'Teknik Komputer & Jaringan']);
        DB::table('kelas')->insert(['nama_kelas' => 'XI', 'jurusan' => 'Teknik Komputer & Jaringan']);
        DB::table('kelas')->insert(['nama_kelas' => 'XII', 'jurusan' => 'Teknik Komputer & Jaringan']);

        DB::table('kelas')->insert(['nama_kelas' => 'X', 'jurusan' => 'Akuntansi']);
        DB::table('kelas')->insert(['nama_kelas' => 'XI', 'jurusan' => 'Akuntansi']);
        DB::table('kelas')->insert(['nama_kelas' => 'XII', 'jurusan' => 'Akuntansi']);

        DB::table('kelas')->insert(['nama_kelas' => 'X', 'jurusan' => 'Pemasaran']);
        DB::table('kelas')->insert(['nama_kelas' => 'XI', 'jurusan' => 'Pemasaran']);
        DB::table('kelas')->insert(['nama_kelas' => 'XII', 'jurusan' => 'Pemasaran']);
    }
}
