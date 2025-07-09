<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [];

        for ($k = 0; $k < 12; $k++) {
            $classes[] = [
                'order_index' => $k + 1,
            ];
        }

        DB::table('user_classes')->insert($classes);
    }
}
