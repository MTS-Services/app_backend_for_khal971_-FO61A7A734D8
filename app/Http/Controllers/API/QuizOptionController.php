<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuizOptionRequest;
use App\Http\Resources\QuizOptionResource;
use App\Http\Services\QuizOptionService;
use App\Models\QuizOption;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QuizOptionController extends Controller
{
    protected QuizOptionService $quizOptionService;

    public function __construct(QuizOptionService $quizOptionService)
    {
        $this->quizOptionService = $quizOptionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function options($quiz_id)
    {
        try {
            if (!$quiz_id) {
                return sendResponse(false, 'Quiz not found', null, Response::HTTP_NOT_FOUND);
            }
            $quizOpitons = $this->quizOptionService->getQuizOptions($quiz_id)->with('quiz')->get();
            if (!$quizOpitons) {
                return sendResponse(false, 'Failed to fetch Quiz Option list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option list fetched successfully', QuizOptionResource::collection($quizOpitons), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Option List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Quiz Option list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuizOptionRequest $request)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $validated = $request->validated();
            $quizOption = $this->quizOptionService->createQuizOption($validated);
            if (!$quizOption) {
                return sendResponse(false, 'Failed to create Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option created successfully', new QuizOptionResource($quizOption), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error(' Quiz Option Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizOption $quiz_option)
    {
        try {
            $quizOption = $this->quizOptionService->getQuizOption($quiz_option->id)->load('quiz.topics.course.subject');
            if (!$quizOption) {
                return sendResponse(false, 'Failed to fetch Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option fetched successfully', new QuizOptionResource($quizOption), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Option Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function update(QuizOptionRequest $request, QuizOption $quiz_option)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $quiz_option = $this->quizOptionService->getQuizOption($quiz_option->id);
            if (!$quiz_option) {
                return sendResponse(false, 'Failed to update Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $validated = $request->validated();
            $quizOption = $this->quizOptionService->updateQuizOption($validated, $quiz_option);
            if (!$quizOption) {
                return sendResponse(false, 'Failed to update Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option updated successfully', new QuizOptionResource($quizOption), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Option Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuizOption $quiz_option)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $quiz_option = $this->quizOptionService->getQuizOption($quiz_option->id);
            if (!$quiz_option) {
                return sendResponse(false, 'Failed to delete Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $this->quizOptionService->deleteQuizOption($quiz_option);
            return sendResponse(true, ' Quiz Option deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Option Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
