<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\BookmarkService;
use Symfony\Component\HttpFoundation\Response;

class BookmarkController extends Controller
{
    protected BookmarkService $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function bookmarkedQuestions()
    {
        $data = $this->bookmarkService->getBookmarkedQuestions();
        return sendResponse(true, 'Bookmarked questions fetched successfully', $data, Response::HTTP_OK);
    }
    public function bookmarkedQuizzes()
    {
        $data = $this->bookmarkService->getBookmarkedQuizzes();
        return sendResponse(true, 'Bookmarked quizzes fetched successfully', $data, Response::HTTP_OK);
    }
}
