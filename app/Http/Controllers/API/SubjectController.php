<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Services\FileService;
use App\Http\Services\SubjectService;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SubjectController extends Controller
{
    protected SubjectService $subjectService;
    protected FileService $fileService;

    public function __construct(SubjectService $subjectService, FileService $fileService)
    {
        $this->subjectService = $subjectService;
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = $this->subjectService->getSubjects();
        if (!$subjects) {
            return sendResponse(false, 'Subject list not found', null, Response::HTTP_NOT_FOUND);
        }
        return sendResponse(true, 'Subject list', $subjects, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return 'Not ok';
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
