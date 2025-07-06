<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserSubjectRequest;
use App\Http\Services\UserSubjectService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserSubjectController extends Controller
{
    private User $user;
    protected UserSubjectService $userSubjectService;
    
    /**
     * @var UserSubjectService $userSubjectService
     */
    public function __construct(UserSubjectService $userSubjectService)
    {
        /**
         * @var UserSubjectService $this->userSubjectService
         */
        $this->userSubjectService = $userSubjectService;
        $this->user = Auth::user();
            
    }
    public function userSubjects()
    {
        $userSubjects = $this->userSubjectService->getUserSubjects()->with('user','subject')->get();
        return response()->json($userSubjects);
    }
     public function store(UserSubjectRequest $request)
    {
        try {
            $this->userSubjectService->storeSubjectsForUser(
                $this->user->id,
                $request->subjects,
                $this->user->id
            );
            return sendResponse(true, 'Subjects selected successfully.', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Subject Selection Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to select subjects', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
