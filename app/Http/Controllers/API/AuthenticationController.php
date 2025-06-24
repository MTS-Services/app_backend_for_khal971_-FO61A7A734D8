<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\LoginRequest;
use App\Http\Requests\API\User\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    /**
     * Register a new account.
     */
    public function register(RegistrationRequest $request)
    {
        try {
            $user = User::create($request->validated());
            $token = $user->createToken('authToken')->accessToken;
            $message = 'Registration successful';
            return sendResponse(true, $message, $user->only('id', 'name', 'email'), Response::HTTP_CREATED, ['token' => $token]);

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return sendResponse(false, 'Registration failed', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login request.
     */
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->validated())) {
                $user = Auth::user();
                $token = $user->createToken('authToken')->accessToken;

                return sendResponse(true, 'Login successful', $user->only('id', 'name', 'email'), Response::HTTP_OK, ['token' => $token]);
            }
            return sendResponse(false, 'Login failed', null, Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return sendResponse(false, 'Login failed', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
