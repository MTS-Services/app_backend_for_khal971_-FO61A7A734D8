<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\QuestionAnswerRequest;
use App\Http\Resources\QuestionAnswerResource;
use App\Http\Services\QuestionAnswerService;
use App\Models\QuestionAnswer;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class QuestionAnswerController extends Controller
{
    protected QuestionAnswerService $questionAnswerService;

    public function __construct(QuestionAnswerService $questionAnswerService)
    {
        $this->questionAnswerService = $questionAnswerService;
    }
    /**
     * Display a listing of the resource.
     */
    public function questionAnswers(int $question_id): JsonResponse
    {
        try{
            if (!$question_id) {
                return sendResponse(false, 'Question ID param is required', null, Response::HTTP_BAD_REQUEST);
            }
            $questionAnswers = $this->questionAnswerService->getQuestionAnswers($question_id)->with('question.questionDetails')->get();
            return sendResponse(true, 'Question answer list fetched successfully', QuestionAnswerResource::collection($questionAnswers), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Question Answer List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch question answer list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function store(QuestionAnswerRequest $request)
    {
        try{  
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }  
            $validated = $request->validated();
            $questionAnswer = $this->questionAnswerService->createQuestionAnswer($validated);
            if (!$questionAnswer) {
                return sendResponse(false, 'Failed to create question answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Question answer created successfully', new QuestionAnswerResource($questionAnswer), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionAnswer $question_answer)
    {
        try{
            $question_answer = $this->questionAnswerService->getQuestionAnswer($question_answer->id)->load('user', 'question.questionDetails.topic.course.subject');
            if (!$question_answer) {
                return sendResponse(false, 'Question answer not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Question answer fetched successfully', new QuestionAnswerResource($question_answer), Response::HTTP_OK);
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
    public function update(QuestionAnswerRequest $request, QuestionAnswer $question_answer)
    {
        try{
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $validated = $request->validated();
            $question_answer = $this->questionAnswerService->updateQuesitonAnswer($validated, $question_answer);
            if (!$question_answer) {
                return sendResponse(false, 'Failed to update question answer', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return sendResponse(true, 'Question answer updated successfully', new QuestionAnswerResource($question_answer), Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionAnswer $question_answer)
    {
        try{
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $question_answer = $this->questionAnswerService->getQuestionAnswer($question_answer->id);
            if (!$question_answer) {
                return sendResponse(false, 'Question answer not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->questionAnswerService->deleteQuestionAnswer($question_answer);
            return sendResponse(true, 'Question answer deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
