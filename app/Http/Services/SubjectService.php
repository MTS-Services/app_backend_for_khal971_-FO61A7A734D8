<?php

namespace App\Http\Services;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\FileService;

class SubjectService
{
    protected FileService $fileService;
    private User $user;

    public function __construct(FileService $fileService)
    {
        $this->user = Auth::user();
        $this->fileService = $fileService;
    }
    /**
     * Fetch subjects, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getSubjects(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {

        $query = Subject::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction);
    }

    public function getSubject($param, string $query_field = 'id'): Subject|null
    {
        $query = Subject::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createSubject($data, $file = null): Subject
    {
        $data['created_by'] = $this->user->id;
        if ($file) {
            $data['icon'] = $this->fileService->uploadFile($file, 'subjects', $data['name']);
        }
        return Subject::create($data);
    }

    public function updateSubject(Subject $subject, $data, $file = null): Subject
    {
        $data['updated_by'] = $this->user->id;
        if ($file) {
            $data['icon'] = $this->fileService->uploadFile($file, 'subjects', $data['name']);
            $this->fileService->fileDelete($subject->icon);
        }
        $subject->update($data);
        return $subject->refresh();
    }

    public function deleteSubject(Subject $subject): bool
    {
        $this->fileService->fileDelete($subject->icon);
        return $subject->delete();
    }

    public function toggleStatus(Subject $subject): Subject
    {
        $subject->update(['status' => !$subject->status, 'updated_by' => $this->user->id]);
        return $subject->refresh();
    }
}
