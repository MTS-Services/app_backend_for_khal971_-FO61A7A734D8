<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkedQuestionResource;
use App\Http\Resources\BookmarkedQuizResource;
use App\Http\Services\BookmarkService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class BookmarkController extends Controller
{
    protected BookmarkService $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function bookmarkedQuestionDetails()
    {
        try {
            $questions = $this->bookmarkService->getBookmarkedQuestionDetails();
            return sendResponse(true, 'Bookmarked question details fetched successfully', BookmarkedQuestionResource::collection($questions), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked questions', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function bookmarkedQuizzes()
    {
        try {
            $quizzes = $this->bookmarkService->getBookmarkedQuizzes();
            return sendResponse(true, 'Bookmarked quizzes fetched successfully', BookmarkedQuizResource::collection($quizzes), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked quizzes', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addQuestionDetailsToBookmark($question_id)
    {
        try {
            $this->bookmarkService->addQuestionDetailsToBookmark($question_id);
            return sendResponse(true, 'Question added to bookmark successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to add question to bookmark', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeQuestionDetailsFromBookmark($question_id)
    {
        try {
            $this->bookmarkService->removeQuestionDetailsFromBookmark($question_id);
            return sendResponse(true, 'Question removed from bookmark successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to remove question from bookmark', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addQuizToBookmark($quiz_id)
    {
        try {
            $this->bookmarkService->addQuizToBookmark($quiz_id);
            return sendResponse(true, 'Quiz added to bookmark successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to add quiz to bookmark', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function removeQuizFromBookmark($quiz_id)
    {
        try {
            $this->bookmarkService->removeQuizFromBookmark($quiz_id);
            return sendResponse(true, 'Quiz removed from bookmark successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to remove quiz from bookmark', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
