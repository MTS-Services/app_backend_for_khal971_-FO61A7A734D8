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
            SubjectSeeder::class,
            SubjectTranslationSeeder::class,
            CourseSeeder::class,
            CourseTranslationSeeder::class,
            UserSeeder::class,
            TopicSeeder::class,
            TopicTranslationSeeder::class,
            QuestionTypeSeeder::class,
            QuestionSeeder::class,
            QuestionOptionSeeder::class,
            PlanSeeder::class,
            PlanFeatureSeeder::class,
            SubscriptionSeeder::class,
            PaymentSeeder::class,

        ]);
    }
}
