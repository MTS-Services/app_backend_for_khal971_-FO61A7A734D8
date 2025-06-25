<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class CourseService
{
    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Fetch Courses, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getCourses(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = Course::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }

    public function getCourse($param, string $query_field = 'id'): Course|null
    {
        $query = Course::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createCourse($data): Course
    {
        $data['created_by'] = $this->user->id;
        return Course::create($data)->refresh();
    }

    public function updateCourse(Course $Course, $data): Course
    {
        $data['updated_by'] = $this->user->id;
        $Course->update($data);
        return $Course->refresh();
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
