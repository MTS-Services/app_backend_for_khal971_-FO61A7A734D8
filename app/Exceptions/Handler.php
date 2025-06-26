<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Throwable;
use TypeError;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        // If for some reason a non-JSON request reaches here AND you still want to redirect:
        // return redirect()->guest(route('web.login')); // Assuming you have a 'web.login' route
        // For a pure API, this else block can usually be removed or throw an error.
        return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED); // Default to JSON if not expecting JSON
    }


    public function render($request, Throwable $exception)
    {
        // For API requests only
        if ($request->is('api/*') || $request->expectsJson()) {

            // Model not found
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested resource not found',
                    'data' => null
                ], Response::HTTP_NOT_FOUND);
            }

            // Invalid route or missing param
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or missing resource identifier',
                    'data' => null
                ], Response::HTTP_NOT_FOUND);
            }

            // Global catch for controller parameter type errors
            if ($exception instanceof TypeError && str_contains($exception->getMessage(), 'must be of type')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid route parameter type',
                    'data' => null
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Validation errors
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'data' => $exception->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Default fallback
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $exception->getMessage(),
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
