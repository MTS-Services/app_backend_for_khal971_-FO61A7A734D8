<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuizRequest;
use App\Http\Services\QuizService;
use App\Models\Quiz;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }
    /**
     * Display a listing of the resource.
     */
    public function quizzes($topic_id): JsonResponse
    {
        try {
            if (!$topic_id) {
                return sendResponse(false, 'Topic not found', null, Response::HTTP_NOT_FOUND);
            }
            $quizzes = $this->quizService->getQuizzes($topic_id);
            // $quizzes = $this->quizService->getQuizzes($topic_id)->with('topics')->get();
            return sendResponse(true, ' Quiz list fetched successfully', $quizzes, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Quiz list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuizRequest $request): JsonResponse
    {

        try {
            $validated = $request->validated();
            $quiz = $this->quizService->createQuiz($validated);


            if (!$quiz) {

                return sendResponse(false, 'Failed to create Quiz', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz created successfully', $quiz, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error(' Quiz Create Error: ' . $e->getMessage(), [
                'request' => $request->all(),
            ]);
            return sendResponse(false, 'An error occurred while creating the Quiz. Please try again later.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz): JsonResponse
    {
        try {
            $quiz = $this->quizService->getQuiz($quiz->id)->load('topics');
            if (!$quiz) {
                return sendResponse(false, 'Quiz not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Quiz fetched successfully', $quiz, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Quiz', null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function update(QuizRequest $request, Quiz $quiz): JsonResponse
    {
        try {
            $quiz = $this->quizService->getQuiz($quiz->id);
            if (!$quiz) {
                return sendResponse(false, ' Quiz not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $file = $request->validated('icon') && $request->hasFile('icon') ? $request->file('icon') : null;
            $quiz = $this->quizService->updateQuiz($quiz, $validated, $file);
            return sendResponse(true, ' Quiz updated successfully', $quiz, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update  Quiz', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        try {
            $quiz = $this->quizService->getQuiz($quiz->id);
            if (!$quiz) {
                return sendResponse(false, ' Quiz not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->quizService->deleteQuiz($quiz);
            return sendResponse(true, ' Quiz deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete Quiz', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(Quiz $quiz): JsonResponse
    {
        try {
            $quiz = $this->quizService->getQuiz($quiz->id);
            if (!$quiz) {
                return sendResponse(false, ' Quiz not found', null, Response::HTTP_NOT_FOUND);
            }
            $quiz = $this->quizService->toggleStatus($quiz);
            return sendResponse(true, " Quiz {$quiz->status_label}  successfully", null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle Quiz status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
