<?php

namespace App\Http\Services;

use App\Models\UserSubject;
use Illuminate\Support\Facades\DB;

class UserSubjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function getUserSubjects(string $orderBy = 'order_index', string $direction = 'asc')
    {

        $query = UserSubject::query();
        return $query->orderBy($orderBy, $direction)->latest();
    }
     public function storeSubjectsForUser(int $userId, array $subjectIds, int $creatorId): void
    {
        DB::transaction(function () use ($userId, $subjectIds, $creatorId) {
            // Remove old selections
            UserSubject::where('user_id', $userId)->delete();

            // Insert new selections
            foreach ($subjectIds as $index => $subjectId) {
                UserSubject::create([
                    'order_index' => $index + 1,
                    'user_id' => $userId,
                    'subject_id' => $subjectId,
                    'created_by' => $creatorId,
                    'updated_by' => $creatorId,
                ]);
            }
        });
    }
}
