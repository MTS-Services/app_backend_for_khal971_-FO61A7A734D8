<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuestionType::create(
            [
                'name' => 'Multiple Choice',
                'description' => 'A question with multiple options, where only one option is correct.',
            ],
        );

        QuestionType::create(
            [
                'name' => 'True/False',
                'description' => 'A question that can be answered with either true or false.',
            ]
        );
    }
}
