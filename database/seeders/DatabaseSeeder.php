<?php

namespace Database\Seeders;

use App\Models\TargetEmail;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::insert([
            [
                "name" => "admin",
                "role" => "admin",
                "password" => bcrypt("mpk58")
            ]
        ]);

        User::insert([
            [
                "name" => "wakil",
                "role" => "wakil",
                "password" => bcrypt("wakilsmkn10")
            ]
        ]);

        TargetEmail::insert([
            ['email' => 'ahmadfagih.arrifai@gmail.com', 'label' => 'Ahmad Fagih', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'yunitakamali72@gmail.com', 'label' => 'Yunita Kamali', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'mujahidrobbanisholahudin@gmail.com', 'label' => 'Mujahid Robbani', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'desita1412@gmail.com', 'label' => 'Desita', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'sayutiazwarmi67@gmail.com', 'label' => 'Sayuti Azwarmi', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
