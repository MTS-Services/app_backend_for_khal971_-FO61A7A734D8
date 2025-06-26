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
                'name' => 'English',
                'is_premium' => false,
            ],
            [
                'name' => 'Mathematics',
                'is_premium' => false,
            ],
            [
                'name' => 'Science',
                'is_premium' => false,
            ],
        ]);
    }
}
