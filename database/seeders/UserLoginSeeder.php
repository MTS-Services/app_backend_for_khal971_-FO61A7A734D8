<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_logins')->insert([
            [
                'order_index' => 101,
                'user_id' => 1,
                'ip' => '192.168.1.10',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'region' => 'Dhaka',
                'lat' => 23.8103,
                'lon' => 90.4125,
                'device' => 'Desktop',
                'browser' => 'Chrome',
                'platform' => 'Windows 10',
                'last_login_at' => Carbon::now(),
            ],
            [
                'order_index' => 102,
                'user_id' => 2,
                'ip' => '203.0.113.5',
                'country' => 'India',
                'city' => 'Kolkata',
                'region' => 'West Bengal',
                'lat' => 22.5726,
                'lon' => 88.3639,
                'device' => 'Mobile',
                'browser' => 'Safari',
                'platform' => 'iOS',
                'last_login_at' => Carbon::now(),
            ]
        ]);
    }
}
