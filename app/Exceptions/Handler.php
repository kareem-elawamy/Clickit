<?php

namespace App\Exceptions;

use App\Traits\ErrorLogsTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use ErrorLogsTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // --- API / JSON requests: always return structured JSON, never HTML ---
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        // --- Web requests: keep the existing 404 redirect logic ---
        if ($this->isHttpException($exception) && $exception?->getStatusCode() == 404) {
            $redirectUrl = $this->storeErrorLogsUrl(url: $request->fullUrl(), statusCode: $exception->getStatusCode());
            if ($redirectUrl && isset($redirectUrl['redirect_url'])) {
                return redirect(to: $redirectUrl['redirect_url'], status: ($redirectUrl['redirect_status'] ?? '301'));
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert any throwable into a typed JSON API response.
     */
    protected function handleApiException($request, Throwable $exception)
    {
        // 401 – Unauthenticated / missing token
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json([
                'errors' => [['code' => 'unauthenticated', 'message' => 'Unauthenticated. Please provide a valid token.']],
            ], 401);
        }

        // 422 – Validation failure
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'errors' => collect($exception->errors())
                    ->map(fn($messages, $field) => ['code' => $field, 'message' => $messages[0]])
                    ->values(),
            ], 422);
        }

        // 404 – Eloquent model not found
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $model = class_basename($exception->getModel());
            return response()->json([
                'errors' => [['code' => 'not_found', 'message' => "{$model} not found."]],
            ], 404);
        }

        // 404 – Route / URL not found
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json([
                'errors' => [['code' => 'not_found', 'message' => 'The requested endpoint does not exist.']],
            ], 404);
        }

        // 405 – Method not allowed
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return response()->json([
                'errors' => [['code' => 'method_not_allowed', 'message' => 'HTTP method not allowed on this endpoint.']],
            ], 405);
        }

        // Other HTTP exceptions (403, 429, etc.) – preserve their status code
        if ($this->isHttpException($exception)) {
            return response()->json([
                'errors' => [['code' => 'http_error', 'message' => $exception->getMessage() ?: 'HTTP error.']],
            ], $exception->getStatusCode());
        }

        // 500 – Generic server error
        $message = config('app.debug')
            ? $exception->getMessage()
            : 'An unexpected server error occurred. Please try again later.';

        return response()->json([
            'errors' => [['code' => 'server_error', 'message' => $message]],
        ], 500);
    }
}
