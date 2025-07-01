<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CourseRequest;
use App\Http\Services\CourseService;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $courses = $this->courseService->getCourses()->with('subject')->get();
            return sendResponse(true, 'Course list fetched successfully', $courses, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Course List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch course list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $Course = $this->courseService->createCourse($validated);
            return sendResponse(true, 'Course created successfully', $Course, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Course Create Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to create Course', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): JsonResponse
    {
        try {
            $Course = $this->courseService->getCourse($course->id)->load('subject');
            if (!$Course) {
                return sendResponse(false, 'Course not found', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Course fetched successfully', $Course, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Course Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch course', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        try {
            $course = $this->courseService->getCourse($course->id);
            if (!$course) {
                return sendResponse(false, 'Course not found', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $courses = $this->courseService->updateCourse($course, $validated);
            return sendResponse(true, 'Course updated successfully', $courses, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Course Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update course', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): JsonResponse
    {
        try {
            $course = $this->courseService->getCourse($course->id);
            if (!$course) {
                return sendResponse(false, 'Course not found', null, Response::HTTP_NOT_FOUND);
            }
            $this->courseService->deleteCourse($course);
            return sendResponse(true, 'Course deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Course Delete Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to delete course', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(Course $course): JsonResponse
    {
        try {
            $course = $this->courseService->getCourse($course->id);
            if (!$course) {
                return sendResponse(false, 'Course not found', null, Response::HTTP_NOT_FOUND);
            }
            $Course = $this->courseService->toggleStatus($course);
            return sendResponse(true, "Course {$Course->status_label}  successfully", Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Course Status Toggle Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to toggle course status', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
