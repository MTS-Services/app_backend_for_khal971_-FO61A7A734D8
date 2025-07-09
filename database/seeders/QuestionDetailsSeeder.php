<?php

namespace Database\Seeders;

use App\Models\QuestionDetails;
use App\Models\QuestionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuestionDetails::factory()->count(10)->create();
    }
}
