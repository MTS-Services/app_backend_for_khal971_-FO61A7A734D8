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
                'order_index' => 101,
                'name' => 'Mathematics',
            ],
            [
                'order_index' => 102,
                'name' => 'Science',
            ],
            [
                'order_index' => 103,
                'name' => 'History',
            ],
        ]);
    }
}
