<?php

namespace App\Http\Services;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SubjectService
{
    // public function getSubjects($orderBy = 'order_index', $order = 'asc'): Subject|Collection
    // {
    //     $query = Subject::orderBy($orderBy, $order);
    //     if (!Auth::user()->is_premium) {
    //         $query->take(12);
    //     }
    //     $query->latest();
    //     return $query;
    // }
    // public function getSubject($param, string $query_field = 'id'): Subject|null
    // {
    //     $query = Subject::query();
    //     if (!Auth::user()->is_premium) {
    //         $query->take(12);
    //     }
    //     return $query->where($query_field, $param)->first();
    // }
    // public function createSubject($data): Subject
    // {
    //     $data['created_by'] = Auth::user()->id;
    //     return Subject::create($data);
    // }

    // public function updateSubject(Subject $subject, $data): bool
    // {
    //     $data['updated_by'] = Auth::user()->id;
    //     return $subject->update($data);
    // }
}
