<?php

namespace Database\Seeders;

use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProgress::factory(100)->create();
        
    }
}
