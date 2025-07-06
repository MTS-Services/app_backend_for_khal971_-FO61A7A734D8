<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgressMilestoneTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('progress_milestone_translations')->insert([
            // English (EN)
            [
                'progress_milestone_id' => 1,
                'language' => 'en',
                'content_type' => 'subject',
                'milestone_type' => 'completion',
                'requirement_description' => 'Complete all items in the subject.',
                'badge_name' => 'Subject Master',
                'title' => 'Subject Completed!',
                'description' => 'You have completed all lessons in the subject.',
                'celebration_message' => 'Amazing! You mastered the subject!',
            ],
            [
                'progress_milestone_id' => 2,
                'language' => 'en',
                'content_type' => 'quiz',
                'milestone_type' => 'accuracy',
                'requirement_description' => 'Achieve 90% accuracy in a quiz.',
                'badge_name' => 'Quiz Ace',
                'title' => 'Accuracy Champ!',
                'description' => 'Excellent accuracy in quizzes.',
                'celebration_message' => 'Well done! You answered with great accuracy!',
            ],
            [
                'progress_milestone_id' => 3,
                'language' => 'en',
                'content_type' => 'overall',
                'milestone_type' => 'streak',
                'requirement_description' => 'Maintain a 7-day activity streak.',
                'badge_name' => 'Weekly Warrior',
                'title' => '7-Day Streak!',
                'description' => 'Consistent progress for a full week.',
                'celebration_message' => 'Keep the fire alive!',
            ],

            // Spanish (ES)
            [
                'progress_milestone_id' => 1,
                'language' => 'es',
                'content_type' => 'subject',
                'milestone_type' => 'completion',
                'requirement_description' => 'Completa todos los elementos del tema.',
                'badge_name' => 'Maestro de la Materia',
                'title' => '¡Tema Completado!',
                'description' => 'Has completado todas las lecciones del tema.',
                'celebration_message' => '¡Increíble! ¡Dominaste el tema!',
            ],
            [
                'progress_milestone_id' => 2,
                'language' => 'es',
                'content_type' => 'quiz',
                'milestone_type' => 'accuracy',
                'requirement_description' => 'Alcanza un 90% de precisión en un cuestionario.',
                'badge_name' => 'As del Quiz',
                'title' => '¡Campeón de Precisión!',
                'description' => 'Excelente precisión en los cuestionarios.',
                'celebration_message' => '¡Buen trabajo! ¡Respondiste con gran precisión!',
            ],
            [
                'progress_milestone_id' => 3,
                'language' => 'es',
                'content_type' => 'overall',
                'milestone_type' => 'streak',
                'requirement_description' => 'Mantén una racha de actividad de 7 días.',
                'badge_name' => 'Guerrero Semanal',
                'title' => '¡Racha de 7 Días!',
                'description' => 'Progreso constante durante una semana completa.',
                'celebration_message' => '¡Sigue así!',
            ],

            // Arabic (AR)
            [
                'progress_milestone_id' => 1,
                'language' => 'ar',
                'content_type' => 'subject',
                'milestone_type' => 'completion',
                'requirement_description' => 'أكمل جميع العناصر في الموضوع.',
                'badge_name' => 'سيد الموضوع',
                'title' => 'اكتمل الموضوع!',
                'description' => 'لقد أكملت جميع الدروس في الموضوع.',
                'celebration_message' => 'رائع! لقد أتقنت الموضوع!',
            ],
            [
                'progress_milestone_id' => 2,
                'language' => 'ar',
                'content_type' => 'quiz',
                'milestone_type' => 'accuracy',
                'requirement_description' => 'حقق دقة بنسبة 90٪ في الاختبار.',
                'badge_name' => 'نجم الاختبار',
                'title' => 'بطل الدقة!',
                'description' => 'دقة رائعة في الاختبارات.',
                'celebration_message' => 'عمل رائع! أجبت بدقة عالية!',
            ],
            [
                'progress_milestone_id' => 3,
                'language' => 'ar',
                'content_type' => 'overall',
                'milestone_type' => 'streak',
                'requirement_description' => 'حافظ على نشاط لمدة 7 أيام متتالية.',
                'badge_name' => 'محارب الأسبوع',
                'title' => 'سلسلة من 7 أيام!',
                'description' => 'تقدم ثابت لمدة أسبوع كامل.',
                'celebration_message' => 'تابع العمل الرائع!',
            ],
        ]);
    }
}
