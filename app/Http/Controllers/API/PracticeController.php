<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PracticeQuestionResource;
use App\Http\Resources\PracticeQuizResource;
use App\Http\Services\PracticeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Practice;
use Illuminate\Support\Facades\Log;

class PracticeController extends Controller
{
    protected PracticeService $practiceService;

    public function __construct(PracticeService $practiceService)
    {
        $this->practiceService = $practiceService;
    }

    public function questionDetails()
    {
        try {
            $questions = $this->practiceService->getQuestionDetails();
            return sendResponse(true, 'Practice questions fetched successfully', PracticeQuestionResource::collection($questions), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked questions', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function quizzes()
    {
        try {
            $quizzes = $this->practiceService->getQuizzes();
            return sendResponse(true, 'Practice quizzes fetched successfully', PracticeQuizResource::collection($quizzes), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked quizzes', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function topics()
    {
        try {
            $topics = $this->practiceService->getTopics();
            return sendResponse(true, 'Practice topics fetched successfully', $topics, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked topics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function courses()
    {
        try {
            $courses = $this->practiceService->getCourses();
            return sendResponse(true, 'Practice courses fetched successfully', $courses, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked courses', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
