<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseService
{
    private User $user;
    protected string $lang;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }
    public static function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    /**
     * Fetch Courses, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getCourses(int $subject_id, string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = Course::counts()->with(['subject', 'translations', 'practice'])->where('subject_id', $subject_id);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }

    public function getCourse($param, string $query_field = 'id'): Course|null
    {
        $query = Course::counts()->with(['subject', 'translations']);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createCourse($data): Course|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $Course = Course::create($data);
                CourseTranslation::create(['course_id' => $Course->id, 'language' => $this->lang, 'name' => $data['name']]);
                TranslateModelJob::dispatch(Course::class, CourseTranslation::class, 'course_id', $Course->id, ['name'], $this->lang);
                $Course = $Course->refresh()->load(['translations', 'subject']);
                return $Course;
            });
        } catch (\Exception $e) {
            Log::error('Course Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateCourse(Course $Course, $data): Course|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($Course, $data) {
                $Course->update($data);
                CourseTranslation::updateOrCreate(['course_id' => $Course->id, 'language' => $this->lang], ['name' => $data['name']]);
                TranslateModelJob::dispatch(Course::class, CourseTranslation::class, 'course_id', $Course->id, ['name'], $this->lang);
                $Course = $Course->refresh()->load(['translations', 'subject']);
                return $Course;
            });
        } catch (\Exception $e) {
            Log::error('Course Update Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteCourse(Course $Course): bool
    {
        return $Course->delete();
    }

    public function toggleStatus(Course $Course): Course|null
    {
        $Course->update(['status' => !$Course->status, 'updated_by' => $this->user->id]);
        return $Course->refresh();
    }
}
