<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('subscriptions')->insert([
            [
                'user_id' => 1,
                'plan_id' => 1,
                'name' => 'Basic Plan',
                'quantity' => 1,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonths(1),
                'payment_method' => 'stripe',
                'payment_frequency' => 'monthly',
            ],
            [
                'user_id' => 2,
                'plan_id' => 2,
                'name' => 'Pro Annual Plan',
                'quantity' => 1,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonths(12),
                'payment_method' => 'google',
                'payment_frequency' => 'yearly',
            ]
        ]);
    }
}
