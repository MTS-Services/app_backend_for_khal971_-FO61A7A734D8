<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('plan_features')->insert([
            [
                'plan_id' => 1,
                'name' => 'Single User Access',
                'description' => 'Access for one user only.',
            ],
            [
                'plan_id' => 1,
                'name' => 'Email Support',
                'description' => 'Get support via email.',
            ],
            [
                'plan_id' => 2,
                'name' => 'Multiple Users',
                'description' => 'Access for up to 3 users.',
            ],
        ]);
    }
}
