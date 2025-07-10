<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuizAnswerRequest;
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
            $quiz_answers = $this->quizAnswerService->getQuizAnswers($quiz_id)->with('quiz.topics')->get();
            return sendResponse(true, 'Quiz Answers Fetched Successfully', $quiz_answers, Response::HTTP_OK);
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
            $validated = $request->validated();
            $quizAnswer = $this->quizAnswerService->createQuizAnswer($validated);
            if (!$quizAnswer) {
                return response()->json(['message' => 'Failed to create Quiz Answer'], 500);
            }
            return response()->json(['message' => 'Quiz Answer created successfully', 'data' => $quizAnswer], 201);
        } catch (\Exception $e) {
            Log::error('Quiz Answer Create Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create Quiz Answer', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizAnswer $quiz_answer)
    {
        try {
            $quiz_answer = $this->quizAnswerService->getQuizAnswer($quiz_answer->id)->with('quiz.topics.course.subject', 'user')->first();
            if (!$quiz_answer) {
                return response()->json(['message' => 'No Quiz Answer Found'], 404);
            }
            return response()->json(['message' => 'Quiz Answer Fetched Successfully', 'data' => $quiz_answer], 200);
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                return response()->json(['message' => 'No Quiz Answer Found', 'error' => $e->getMessage()], 404);
            } else {
                return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
            }
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
            $quiz_answer = $this->quizAnswerService->getQuizAnswer($quiz_answer->id);
            $validated = $request->validated();
            $quizAnswer = $this->quizAnswerService->updateQuesitonAnswer($validated, $quiz_answer);
            if (!$quizAnswer) {
                return response()->json(['message' => 'Failed to update Quiz Answer'], 500);
            }
            return response()->json(['message' => 'Quiz Answer updated successfully', 'data' => $quizAnswer], 200);
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
            $quiz_answer = $this->quizAnswerService->getQuizAnswer($quiz_answer->id);
            if (!$quiz_answer) {
                return response()->json(['message' => 'No Quiz Answer Found'], 404);
            }
            $this->quizAnswerService->deleteQuizAnswer($quiz_answer);
            return response()->json(['message' => 'Quiz Answer deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Quiz Answer Delete Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete Quiz Answer', 'error' => $e->getMessage()], 500);
        }
    }
}
