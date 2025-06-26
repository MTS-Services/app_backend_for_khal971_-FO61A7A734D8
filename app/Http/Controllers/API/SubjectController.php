<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\SubjectRequest;
use App\Http\Services\FileService;
use App\Http\Services\SubjectService;
use App\Jobs\TestQueueJob;
use App\Jobs\TranslateModelJob;
use App\Models\Subject;
use App\Models\SubjectTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SubjectController extends Controller
{
    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $subjects = $this->subjectService->getSubjects()->get();
            return sendResponse(true, 'Subject list fetched successfully', $subjects, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Subject List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch subject list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubjectRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $file = $request->validated('icon') && $request->hasFile('icon') ? $request->file('icon') : null;
            $subject = $this->subjectService->createSubject($validated, $file);


            if (!$subject) {
                TranslateModelJob::dispatch(Subject::class, SubjectTranslation::class, 'subject_id', $subject->id, ['name']);
                return sendResponse(false, 'Failed to create subject', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Subject created successfully', $subject, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Subject Create Error: ' . $e->getMessage(), [
                'request' => $request->all(),
            ]);
            return sendResponse(false, 'An error occurred while creating the subject. Please try again later.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): JsonResponse
    {
        try {

            dd($subject->load('translations'));
            $subject = $this->subjectService->getSubject($subject->id);
            if (!$subject) {
                return sendResponse(false, 'Subject not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Subject fetched successfully', $subject, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Subject Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch subject', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubjectRequest $request, Subject $subject): JsonResponse
    {

        try {
            $subject = $this->subjectService->getSubject($subject->id);
            if (!$subject) {
                return sendResponse(false, 'Subject not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $file = $request->validated('icon') && $request->hasFile('icon') ? $request->file('icon') : null;
            $subject = $this->subjectService->updateSubject($subject, $validated, $file);
            return sendResponse(true, 'Subject updated successfully', $subject, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Subject Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update subject', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        try {
            $subject = $this->subjectService->getSubject($subject->id);
            if (!$subject) {
                return sendResponse(false, 'Subject not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->subjectService->deleteSubject($subject);
            return sendResponse(true, 'Subject deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Subject Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete subject', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(Subject $subject): JsonResponse
    {
        try {
            $subject = $this->subjectService->getSubject($subject->id);
            if (!$subject) {
                return sendResponse(false, 'Subject not found', null, Response::HTTP_NOT_FOUND);
            }
            $subject = $this->subjectService->toggleStatus($subject);
            return sendResponse(true, "Subject {$subject->status_label}  successfully", null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Subject Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle subject status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
