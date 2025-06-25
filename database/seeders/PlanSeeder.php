<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('plans')->insert([
            [
                'order_index' => 101,
                'name' => 'Basic',
                'description' => 'Basic plan for individuals.',
                'price' => 9.99,
                'duration' => 1,
                'stripe_price_id' => 'price_abc123',
                'apple_product_id' => 'com.app.basic',
                'google_product_id' => 'com.app.basic',
                'features' => json_encode(['1 user', 'Email support']),
            ],
            [
                'order_index' => 102,
                'name' => 'Pro',
                'description' => 'Pro plan with advanced features.',
                'price' => 29.99,
                'duration' => 3,
                'stripe_price_id' => 'price_def456',
                'apple_product_id' => 'com.app.pro',
                'google_product_id' => 'com.app.pro',
                'features' => json_encode(['Up to 3 users', 'Priority support']),
            ],
        ]);
    }
}
