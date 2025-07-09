<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserUpdatedRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function user(Request $request)
    {
        try {
            $user = $this->userService->getUser($request->user()->id)->load("userClass");
            if (!$user) {
                return sendResponse(false, 'User not authenticated', null, Response::HTTP_UNAUTHORIZED);
            }
            return sendResponse(true, 'User details fetched successfully', $user, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function userAccessByAdmin(int $id)
    {
        try {
            if (!$id) {
                return sendResponse(false, 'User ID parameter is required.', null, Response::HTTP_BAD_REQUEST);
            }
            $authenticatedUser = request()->user();
            if (!$authenticatedUser || !$authenticatedUser->is_admin) {
                return sendResponse(false, 'Unauthorized access.', null, Response::HTTP_UNAUTHORIZED);
            }
            $user = $this->userService->getUser($id);
            if (!$user) {
                return sendResponse(false, 'User not found.', null, Response::HTTP_NOT_FOUND);
            }
            $user->load('userClass');
            return sendResponse(true, 'User details fetched successfully.', $user, Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('User Fetch Error', ['message' => $e->getMessage(), 'user_id' => $id]);

            return sendResponse(false, 'An error occurred while fetching user details.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function userUpdateByAdmin(int $id, UserUpdatedRequest $request)
    {
        try {
            if (!$id) {
                return sendResponse(false, 'User ID parameter is required.', null, Response::HTTP_BAD_REQUEST);
            }
            $authenticatedUser = request()->user();
            if (!$authenticatedUser || !$authenticatedUser->is_admin) {
                return sendResponse(false, 'Unauthorized access.', null, Response::HTTP_UNAUTHORIZED);
            }
            $user = $this->userService->getUser($id);
            if (!$user) {
                return sendResponse(false, 'User not found.', null, Response::HTTP_NOT_FOUND);
            }
            $user = $this->userService->updateUser($user, $request->validated(), $request->file('image'));
            return sendResponse(true, 'User details updated successfully.', $user, Response::HTTP_OK);
        } catch (\Throwable $e) {
            Log::error('User Update Error', ['message' => $e->getMessage(), 'user_id' => $id]);
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return sendResponse(false, 'Validation Error', $e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return sendResponse(false, 'An error occurred while updating user details.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    /**
     * Get paginated user list (authenticated).
     */
    public function users($perPage = 10)
    {
        try {
            if (request()->user()->is_admin !== true) {
                return sendResponse(false, 'Unauthorized access', null, Response::HTTP_UNAUTHORIZED);
            }
            $users = $this->userService->getUsers()->with('userClass')->paginate($perPage);
            return sendResponse(true, 'User list fetched successfully', $users, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateUser(UserRequest $request)
    {
        try {
            $user = $this->userService->getUser($request->user()->id);
            $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
            $validated = $request->validated();
            $user = $this->userService->updateUser($user, $validated, $file);
            return sendResponse(true, 'User updated successfully', $user, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Update Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to update user', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUsers(Request $request)
    {
        $users = User::all();
        return sendResponse(true, 'Users fetched successfully', $users, Response::HTTP_OK);
    }
}
