<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@dev.com',
            'phone' => '1234567890',
            'is_admin' => true,
            'password' => 'admin@dev.com',
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@dev.com',
            'phone' => '1234567891',
            'password' => 'user@dev.com',
        ]);
    }
}
