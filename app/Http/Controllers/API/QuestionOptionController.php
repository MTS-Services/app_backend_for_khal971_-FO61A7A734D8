<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuestionOptionRequest;
use App\Http\Services\QuestionOptionService;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QuestionOptionController extends Controller
{
    protected QuestionOptionService $questionOptionService;

    public function __construct(QuestionOptionService $questionOptionService)
    {
        $this->questionOptionService = $questionOptionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $question_options = $this->questionOptionService->getQuestionOptions()->get();
            return sendResponse(true, 'Question list fetched successfully', $question_options, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch question list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function store(QuestionOptionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $question_option = $this->questionOptionService->createQuestionOption($validated);

            if (!$question_option) {
                return sendResponse(false, 'Failed to create Question', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Question created successfully', $question_option, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Question Create Error: ' . $e->getMessage(), [
                'request' => $request->all(),
            ]);
            return sendResponse(false, 'An error occurred while creating the Question. Please try again later.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionOption $question_option): JsonResponse
    {
        try{
            $question_option = $this->questionOptionService->getQuestionOption($question_option->id);
            if (!$question_option) {
                return sendResponse(false, 'Question not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Question fetched successfully', $question_option, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Question', null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function update(QuestionOptionRequest $request, QuestionOption $question_option): JsonResponse
    {

        try {
            $question_option = $this->questionOptionService->getQuestionOption($question_option->id);
            if (!$question_option) {
                return sendResponse(false, 'Question not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $question_option = $this->questionOptionService->updateQuestionOption($question_option, $validated);
            return sendResponse(true, 'Question updated successfully', $question_option, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update Question', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionOption $question_option): JsonResponse
    {
        try {
            $question_option = $this->questionOptionService->getQuestionOption($question_option->id);
            if (!$question_option) {
                return sendResponse(false, 'Question not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->questionOptionService->deleteQuestion($question_option);
            return sendResponse(true, 'Question deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete Question', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(QuestionOption $question_option): JsonResponse
    {
        try {
            $question_option = $this->questionOptionService->getQuestionOption($question_option->id);
            if (!$question_option) {
                return sendResponse(false, 'Question not found', null, Response::HTTP_NOT_FOUND);
            }
            $question_option = $this->questionOptionService->toggleStatus($question_option);
            return sendResponse(true, "Question {$question_option->status_label}  successfully", null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle Question status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

