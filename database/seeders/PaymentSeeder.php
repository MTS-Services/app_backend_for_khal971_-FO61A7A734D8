<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payments')->insert([
            [
                'user_id' => 1,
                'subscription_id' => 1,
                'amount' => 9.99,
                'payment_method' => 'stripe',
                'status' => Payment::STATUS_COMPLETED,
            ],
            [
                'user_id' => 2,
                'subscription_id' => 2,
                'amount' => 99.99,
                'payment_method' => 'google',
                'status' => Payment::STATUS_PENDING,
            ]
        ]);
    }
}
