<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\Subject;
use App\Models\SubjectTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;

class SubjectService
{
    protected FileService $fileService;
    protected DeeplTranslateService $deepl;
    private User $user;

    public function __construct(FileService $fileService, DeeplTranslateService $deepl)
    {
        $this->user = Auth::user();
        $this->fileService = $fileService;
        $this->deepl = $deepl;
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
        return $query->orderBy($orderBy, $direction)->latest();
    }

    public function getSubject($param, string $query_field = 'id'): Subject|null
    {

        $query = Subject::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free();
        }
        return $query->where($query_field, $param)->first();
    }
    public function createSubject($data, $file = null): Subject|null
    {
        try {
            $data['created_by'] = $this->user->id;
            if ($file) {
                $data['icon'] = $this->fileService->uploadFile($file, 'subjects', $data['name']);
            }

            return DB::transaction(function () use ($data) {
                $subject = Subject::create($data);
                // $transData = [];
                // foreach (allowLangs() as $lang) {
                //     $name = $this->deepl->translate($data['name'], $lang);
                //     $transData[] = ['subject_id' => $subject->id, 'language' => $lang, 'name' => $name];
                // }
                // SubjectTranslation::insert($transData);
                TranslateModelJob::dispatch(Subject::class, SubjectTranslation::class, 'subject_id', $subject->id, ['name']);
                return $subject->refresh();
            });
        } catch (\Exception $e) {
            Log::error('Subject Create Error: ' . $e->getMessage());
            return null;
        }

    }

    public function updateSubject(Subject $subject, $data, $file = null): Subject|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            if ($file) {
                $data['icon'] = $this->fileService->uploadFile($file, 'subjects', $data['name']);
                $this->fileService->fileDelete($subject->icon);
            }
            return DB::transaction(function () use ($subject, $data) {
                $subject->update($data);
                foreach (allowLangs() as $lang) {
                    $name = $this->deepl->translate($data['name'], $lang);
                    SubjectTranslation::updateOrCreate(['subject_id' => $subject->id, 'language' => $lang], ['name' => $name]);
                }
                return $subject->refresh();
            });
        } catch (\Exception $e) {
            Log::error('Subject Update Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteSubject(Subject $subject): bool
    {
        $this->fileService->fileDelete($subject->icon);
        return $subject->delete();
    }

    public function toggleStatus(Subject $subject): Subject|null
    {
        $subject->update(['status' => !$subject->status, 'updated_by' => $this->user->id]);
        return $subject->refresh();
    }
}
