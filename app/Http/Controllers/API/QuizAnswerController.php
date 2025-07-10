<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuizAnswerRequest;
use App\Http\Resources\QuizAnswerResource;
use App\Http\Services\QuizAnswerService;
use App\Models\QuizAnswer;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class QuizAnswerController extends Controller
{
    protected QuizAnswerService $quizAnswerService;

    public function __construct(QuizAnswerService $quizAnswerService)
    {
        $this->quizAnswerService = $quizAnswerService;
    }
    /**
     * Display a listing of the resource.
     */

    public function quizAnswers(int $quiz_id): JsonResponse
    {
        try {
            if (!$quiz_id) {
                return sendResponse(false, 'Quiz ID param is required', null, Response::HTTP_BAD_REQUEST);
            }
            $quiz_answers = $this->quizAnswerService->getQuizAnswers($quiz_id)->get();
            return sendResponse(true, 'Quiz Answers Fetched Successfully', QuizAnswerResource::collection($quiz_answers), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Quiz Answers Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch quiz answers', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuizAnswerRequest $request)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $validated = $request->validated();
            $quizAnswer = $this->quizAnswerService->createQuizAnswer($validated);
            if (!$quizAnswer) {
                return sendResponse(false, 'Failed to create Quiz Answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Quiz Answer created successfully', new QuizAnswerResource($quizAnswer), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Quiz Answer Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create Quiz Answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizAnswer $quiz_answer)
    {
        try {
            $quiz_answer = $this->quizAnswerService->getQuizAnswer($quiz_answer->id)->first();
            if (!$quiz_answer) {
                return sendResponse(false, 'No Quiz Answer Found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Quiz Answer Fetched Successfully', new QuizAnswerResource($quiz_answer), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Quiz Answer Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Quiz Answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuizAnswerRequest $request, QuizAnswer $quiz_answer)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $quiz_answer = $this->quizAnswerService->getQuizAnswer($quiz_answer->id);
            $validated = $request->validated();
            $quizAnswer = $this->quizAnswerService->updateQuesitonAnswer($validated, $quiz_answer);
            if (!$quizAnswer) {
                return sendResponse(false, 'Failed to update Quiz Answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Quiz Answer updated successfully', new QuizAnswerResource($quizAnswer), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Quiz Answer Update Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update Quiz Answer', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuizAnswer $quiz_answer)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $quiz_answer = $this->quizAnswerService->getQuizAnswer($quiz_answer->id);
            if (!$quiz_answer) {
                return sendResponse(false, 'No Quiz Answer Found', null, Response::HTTP_NOT_FOUND);
            }
            $this->quizAnswerService->deleteQuizAnswer($quiz_answer);
            return sendResponse(true, 'Quiz Answer deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Quiz Answer Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete Quiz Answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
