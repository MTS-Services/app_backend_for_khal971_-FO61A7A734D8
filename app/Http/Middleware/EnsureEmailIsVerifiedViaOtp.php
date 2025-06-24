<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Services\AuthenticationService;

class EnsureEmailIsVerifiedViaOtp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->authService->isVerified($user)) {
            // Send OTP automatically if needed (optional)
            $this->authService->generateOtp($user);

            return sendResponse(false, 'Email not verified. OTP sent to your email.', ['otp_expires_at' => $user->otp_expires_at], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
