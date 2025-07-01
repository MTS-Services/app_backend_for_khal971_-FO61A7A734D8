<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuestionTypeRequest;
use App\Http\Services\QuestionTypeService;
use App\Models\QuestionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

use function PHPSTORM_META\type;

class QuestionTypeController extends Controller
{
    protected QuestionTypeService $questionTypeService;

    public function __construct(QuestionTypeService $questionTypeService)
    {
        $this->questionTypeService = $questionTypeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $question_types = $this->questionTypeService->getQuestionTypes()->get();
            return sendResponse(true, 'Question Type list fetched successfully', $question_types, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Type List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch question type list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionTypeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $question_type = $this->questionTypeService->createQuestionType($validated);
            return sendResponse(true, 'Question type created successsfully', $question_type, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Question Type List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch question type list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionType $question_type)
    {
        try {
            $question_type = $this->questionTypeService->getQuestionType($question_type->id);
            if (!$question_type) {
                return sendResponse(false, 'Question Type not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Question Type fetched successfully', $question_type, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Type Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch question type', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionTypeRequest $request, QuestionType $question_type): JsonResponse
    {
         try {
            $question_type = $this->questionTypeService->getQuestionType($question_type->id);
            if (!$question_type) {
                return sendResponse(false, 'Course not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $question_types = $this->questionTypeService->updateQuestionType($question_type, $validated);
            return sendResponse(true, 'Course updated successfully', $question_types, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Course Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update course', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionType $question_type): JsonResponse
    {
        try {
            $question_type = $this->questionTypeService->getQuestionType($question_type->id);
            if (!$question_type) {
                return sendResponse(false, 'Question Type not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->questionTypeService->deleteQuestionType($question_type);
            return sendResponse(true, 'Question Type deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Type Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete question type', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function toggleStatus(QuestionType $question_type): JsonResponse
    {
        try {
            $question_type = $this->questionTypeService->getQuestionType($question_type->id);
            if (!$question_type) {
                return sendResponse(false, 'Question Type not found', null, Response::HTTP_NOT_FOUND);
            }
            $question_type = $this->questionTypeService->toggleStatus($question_type);
            return sendResponse(true, "Question Type {$question_type->status_label}  successfully", null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Type Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle question type status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
