<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuestionDetailsRequest;
use App\Http\Services\QuestionDetailsService;
use App\Models\QuestionDetails;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuestionDetailsController extends Controller
{
    protected QuestionDetailsService $questionDetailsService;

    public function __construct(QuestionDetailsService $questionDetailsService)
    {
        $this->questionDetailsService = $questionDetailsService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $questionDetails = $this->questionDetailsService->getQuestionDetails()->get();
            if (empty($questionDetails)) {
                return sendResponse(false, 'No question details found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Question details fetched successfully', $questionDetails, Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function store(QuestionDetailsRequest $request)
    {
        try {
            $validated = $request->validated();
            $questionDetails = $this->questionDetailsService->createQuestionDetail($validated);
            if (!$questionDetails) {
                return sendResponse(false, 'Failed to create question details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Question details created successfully', $questionDetails, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionDetails $question_detail)
    {
        try{
            $question_detail = $this->questionDetailsService->getQuestionDetail($question_detail->id);
            if (!$question_detail) {
                return sendResponse(false, 'Question details not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Question details fetched successfully', $question_detail, Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function update(QuestionDetailsRequest $request, QuestionDetails $question_detail)
    {
        try {
            $validated = $request->validated();

            $question_detail = $this->questionDetailsService->updateQuestionDetail($question_detail, $validated);

            if (!$question_detail) {
                return sendResponse(false, 'Failed to update question details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return sendResponse(true, 'Question details updated successfully', $question_detail, Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $question_detail = $this->questionDetailsService->getQuestionDetail($id);
            if (!$question_detail) {
                return sendResponse(false, 'Question details not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->questionDetailsService->deleteQuestionDetail($question_detail);
            return sendResponse(true, 'Question details deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function toggleStatus(QuestionDetails $question_detail)
    {
        try {
            $question_detail = $this->questionDetailsService->toggleStatus($question_detail);
            if (!$question_detail) {
                return sendResponse(false, 'Failed to toggle status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Status toggled to ' . $question_detail->status_label . ' successfully', [], Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
