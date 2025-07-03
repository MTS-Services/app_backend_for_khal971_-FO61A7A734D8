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
         DB::table('class_translations')->insert([
            [
                'name' => 'Class One',
            ],
            [
                'name' => 'Clase Uno',
            ],
            [
                'name' => 'الصف الأول',
            ],
        ]);
    }
}
