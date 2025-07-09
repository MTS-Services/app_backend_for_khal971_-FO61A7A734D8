<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SubjectSeeder::class,
            SubjectTranslationSeeder::class,
            UserSubjectSeeder::class,
            UserClassSeeder::class,
            UserClassTranslationSeeder::class,
            CourseSeeder::class,
            CourseTranslationSeeder::class,
            TopicSeeder::class,
            TopicTranslationSeeder::class,
            QuestionDetailsSeeder::class,
            QuestionDetailsTranslationSeeder::class,
            QuestionSeeder::class,
            QuestionTranslationSeeder::class,
            QuestionAnswerSeeder::class,
            QuestionAnswerTranslationSeeder::class,
            QuizSeeder::class,
            QuizTranslationSeeder::class,
            QuizOptionSeeder::class,
            QuizOptionTranslationSeeder::class,
            QuizAnswerSeeder::class,
            PlanSeeder::class,
            PlanFeatureSeeder::class,
            SubscriptionSeeder::class,
            PaymentSeeder::class,

        ]);
    }
}
