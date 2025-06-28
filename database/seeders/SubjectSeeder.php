<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subjects')->insert([
            [
                'is_premium' => false,
            ],
            [

                'is_premium' => false,
            ],
            [

                'is_premium' => false,
            ],
        ]);
    }
}
