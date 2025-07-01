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
            CourseSeeder::class,
            CourseTranslationSeeder::class,
            UserSeeder::class,
            TopicSeeder::class,
            TopicTranslationSeeder::class,
            QuestionTypeSeeder::class,
            QuestionTypeTranslationSeeder::class,
            QuestionSeeder::class,
            QuestionTranslationSeeder::class,
            QuestionOptionSeeder::class,
            QuestionOptionTranslationSeeder::class,
            PlanSeeder::class,
            PlanFeatureSeeder::class,
            SubscriptionSeeder::class,
            PaymentSeeder::class,

        ]);
    }
}
