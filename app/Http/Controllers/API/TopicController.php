<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TopicRequest;
use App\Http\Services\TopicService;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TopicController extends Controller
{
    protected TopicService $topicService;

    public function __construct(TopicService $topicService)
    {
        $this->topicService = $topicService;
    }
    /**
     * Display a listing of the resource.
     */
    public function courseTopics($course_id): JsonResponse
    {
        try {
            if (!$course_id) {
                return sendResponse(false, 'Course ID param is required', null, Response::HTTP_BAD_REQUEST);
            }
            $topics = $this->topicService->getTopics($course_id)->with('course.subject')->get();
            return sendResponse(true, 'Topic list fetched successfully', $topics, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('topic List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch topic list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(TopicRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $topic = $this->topicService->createTopic($validated);
            return sendResponse(true, 'topic created successfully', $topic, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('topic Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create topic', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic): JsonResponse
    {
        try {
            $topic = $this->topicService->getTopic($topic->id);
            if (!$topic) {
                return sendResponse(false, 'topic not found', null, Response::HTTP_NOT_FOUND);
            }
            $topic->load('course.subject');
            return sendResponse(true, 'topic fetched successfully', $topic, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('topic Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch topic', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TopicRequest $request, topic $topic): JsonResponse
    {
        try {
            $topic = $this->topicService->getTopic($topic->id);
            if (!$topic) {
                return sendResponse(false, 'topic not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $topics = $this->topicService->updateTopic($topic, $validated);
            return sendResponse(true, 'topic updated successfully', $topics, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('topic Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update topic', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic): JsonResponse
    {
        try {
            $topic = $this->topicService->getTopic($topic->id);
            if (!$topic) {
                return sendResponse(false, 'topic not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->topicService->deletetopic($topic);
            return sendResponse(true, 'topic deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('topic Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete topic', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(Topic $topic): JsonResponse
    {
        try {
            $topic = $this->topicService->getTopic($topic->id);
            if (!$topic) {
                return sendResponse(false, 'topic not found', null, Response::HTTP_NOT_FOUND);
            }
            $topic = $this->topicService->toggleStatus($topic);
            return sendResponse(true, "topic {$topic->status_label}  successfully", Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('topic Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle topic status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
