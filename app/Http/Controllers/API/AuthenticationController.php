<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\ForgotRequest;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\OtpResentRequest;
use App\Http\Requests\API\Auth\OtpVerifyRequest;
use App\Http\Requests\API\Auth\RegistrationRequest;
use App\Http\Requests\API\Auth\ResetPasswordRequest;
use App\Http\Services\AuthenticationService;
use App\Http\Services\UserService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;


class AuthenticationController extends Controller
{
    protected AuthenticationService $authenticationService;
    protected UserService $userService;
    public function __construct(AuthenticationService $authenticationService, UserService $userService)
    {
        $this->authenticationService = $authenticationService;
        $this->userService = $userService;
    }
    /**
     * Register a new account.
     */
    public function register(RegistrationRequest $request)
    {
        try {
            $user = User::create($request->validated());
            $this->authenticationService->generateOtp($user);
            $token = $user->createToken('authToken')->accessToken;

            return sendResponse(true, 'Registration successful. OTP sent to your email.', $user->only('id', 'name', 'email', 'phone', 'is_admin'), Response::HTTP_CREATED, ['token' => $token]);
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




    public function verifyOtp(OtpVerifyRequest $request)
    {

        try {
            $user = $this->userService->getUser($request->validated('email'), 'email');
            if ($this->authenticationService->verifyOtp($user, $request->validated('otp'))) {
                return sendResponse(true, 'Email verified successfully.', null);
            }
            return sendResponse(false, 'OTP expired.', null, Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage(), [
                'email' => $request->email,
                'otp' => $request->otp ?? null,
            ]);

            return sendResponse(false, 'Something went wrong during verification.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function resendOtp(OtpResentRequest $request)
    {
        $user = $this->userService->getUser($request->validated('email'), 'email');
        $result = $this->authenticationService->resendOtp($user);
        if ($result['blocked']) {
            return sendResponse(false, $result['message'], null, Response::HTTP_TOO_MANY_REQUESTS); // Too Many Requests
        }
        return sendResponse(true, $result['message'], null, Response::HTTP_OK);
    }

    public function forgot(ForgotRequest $request)
    {
        try {
            $user = $this->userService->getUser($request->validated('email'), 'email');
            $this->authenticationService->generateOtp($user);
            $token = Password::createToken($user);
            return sendResponse(true, 'OTP sent successfully.', ['password_reset_token' => $token], Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, 'Something went wrong.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = $this->userService->getUser($request->validated('email'), 'email');
            if (!Password::tokenExists($user, $request->token)) {
                return sendResponse(false, 'Invalid or expired reset token.', [
                    'token' => ['The token is invalid or has expired.']
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $this->authenticationService->resetPassword($user, $request->validated('password'));
            return sendResponse(true, 'Password reset successfully.', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return sendResponse(false, 'Something went wrong.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
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
