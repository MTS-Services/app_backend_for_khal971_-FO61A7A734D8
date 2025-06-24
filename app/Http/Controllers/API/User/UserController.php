<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class UserController extends Controller
{
    public function user(Request $request)
    {

        try {
            $user = $request->user();
            if (!$user) {
                return sendResponse(false, 'User not authenticated', null, Response::HTTP_UNAUTHORIZED);
            }
            return sendResponse(true, 'User details fetched successfully', $user->only('id', 'name', 'email'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User Fetch Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get paginated user list (authenticated).
     */
    public function users($perPage = 10)
    {
        try {
            $users = User::latest()->paginate($perPage);
            return sendResponse(true, 'User list fetched successfully', $users, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('User List Error: ' . $e->getMessage());
            return sendResponse(false, 'Failed to fetch user list', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout the user and revoke token.
     */
    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $request->user()->tokens()->delete();
                return sendResponse(true, 'Logout successful', null, Response::HTTP_OK);
            }
            return sendResponse(false, 'User not authenticated', null, Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return sendResponse(false, 'Logout failed', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
