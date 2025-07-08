<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuizOptionRequest;
use App\Http\Services\QuizOptionService;
use App\Models\QuizOption;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
    public function index()
    {
        try {
            $quizOpitons = $this->quizOptionService->getQuizOptions()->with('quiz.topics')->get();
            if (!$quizOpitons) {
                return sendResponse(false, 'Failed to fetch Quiz Option list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option list fetched successfully', $quizOpitons, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Option List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch Quiz Option list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuizOptionRequest $request)
    {
        try {
            $validated = $request->validated();
            $quizOption = $this->quizOptionService->createQuizOption($validated);
            if (!$quizOption) {
                return sendResponse(false, 'Failed to create Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option created successfully', $quizOption, Response::HTTP_CREATED);
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
            return sendResponse(true, ' Quiz Option fetched successfully', $quizOption, Response::HTTP_OK);
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
            $quiz_option = $this->quizOptionService->getQuizOption($quiz_option->id);
            if (!$quiz_option) {
                return sendResponse(false, 'Failed to update Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $validated = $request->validated();
            $quizOption = $this->quizOptionService->updateQuizOption($validated, $quiz_option);
            if (!$quizOption) {
                return sendResponse(false, 'Failed to update Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, ' Quiz Option updated successfully', $quizOption, Response::HTTP_OK);
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
            $quiz_option = $this->quizOptionService->getQuizOption($quiz_option->id);
            if (!$quiz_option) {
                return sendResponse(false, 'Failed to delete Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $quiz_option = $this->quizOptionService->deleteQuizOption($quiz_option);
            return sendResponse(true, ' Quiz Option deleted successfully', $quiz_option, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(' Quiz Option Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete Quiz Option', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
