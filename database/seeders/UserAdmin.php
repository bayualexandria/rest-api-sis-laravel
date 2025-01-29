<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => "Bayu Wardana",
            'email' => "wardanabayu555@gmail.com",
            'username' => "9106012508950001",
            'password' => bcrypt("Wardana13@"),
            'status_id' => 1,
            // 'email_verified_at' => null
        ]);
        User::factory()->create([
            'name' => "Bayu Wardana",
            'email' => "wardanabayu503@gmail.com",
            'username' => "9106012508950002",
            'password' => bcrypt("Wardana13@"),
            'status_id' => 2,
            // 'email_verified_at' => null
        ]);
    }
}
