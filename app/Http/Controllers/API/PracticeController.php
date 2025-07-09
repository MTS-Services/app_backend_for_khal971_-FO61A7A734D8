<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\PracticeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Practice;

class PracticeController extends Controller
{
    protected PracticeService $practiceService;

    public function __construct(PracticeService $practiceService)
    {
        $this->practiceService = $practiceService;
    }

    public function quizzes()
    {
        $quizzes = $this->practiceService->getQuizzes();
        return sendResponse(true, 'Practice quizzes fetched successfully', $quizzes, Response::HTTP_OK);
    }
    public function questions()
    {
        $questions = $this->practiceService->getQuestions();
        return sendResponse(true, 'Practice questions fetched successfully', $questions, Response::HTTP_OK);
    }
    public function topics()
    {
        $topics = $this->practiceService->getTopics();
        return sendResponse(true, 'Practice topics fetched successfully', $topics, Response::HTTP_OK);
    }
    public function courses()
    {
        $courses = $this->practiceService->getCourses();
        return sendResponse(true, 'Practice courses fetched successfully', $courses, Response::HTTP_OK);
    }
}
