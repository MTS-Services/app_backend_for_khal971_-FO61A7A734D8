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
            return sendResponse(false, 'Failed to fetch bookmarked questions', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function bookmarkedQuizzes()
    {
        try {
            $quizzes = $this->bookmarkService->getBookmarkedQuizzes();
            return sendResponse(true, 'Bookmarked quizzes fetched successfully', BookmarkedQuizResource::collection($quizzes), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch bookmarked quizzes', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
