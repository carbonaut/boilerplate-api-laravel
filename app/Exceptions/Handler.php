<?php

namespace App\Exceptions;

use App\Models\Phrase;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler {
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * Custom exception message returned in json.
     *
     * @var string
     */
    protected $json_exception_message = 'JSON exception message not set';

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
     *
     * @throws \Exception
     */
    public function report(Throwable $exception) {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @throws \Throwable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception) {
        // On JSON we need to keep the response structure so the apps know what to expect
        if ($request->expectsJson()) {
            // Set a custom message for json
            $this->json_exception_message = class_basename($exception) . ' in ' . basename($exception->getFile()) . ' line ' . $exception->getLine() . ': ' . $exception->getMessage();

            // Custom exception for model not found (used in route model binding)
            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->jsonRender(
                    'Object not found (' . $exception->getMessage() . ')',
                    'ERROR_MODEL_NOT_FOUND',
                    404
                );
            }

            // Custom auth exception response
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return $this->jsonRender(
                    'Unauthenticated',
                    'ERROR_UNAUTHENTICATED',
                    401
                );
            }

            // Custom Validator exception response
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                $this->json_exception_message = collect($exception->errors())->flatten()->implode(' ');

                return $this->jsonRender(
                    $this->json_exception_message,
                    collect($exception->errors())->flatten()->first(),
                    422,
                    false
                );
            }

            // Maintenance Mode exception response
            if ($exception instanceof \Illuminate\Foundation\Http\Exceptions\MaintenanceModeException) {
                $this->json_exception_message = 'API under maintenance.';

                return $this->jsonRender(
                    $this->json_exception_message,
                    'ERROR_MAINTENANCE_MODE',
                    418
                );
            }

            // Unauthorized exception response
            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->jsonRender(
                    'User has no permission to access the resource.',
                    'ERROR_UNAUTHORIZED',
                    403,
                );
            }

            // File not found on S3
            if ($exception instanceof \League\Flysystem\FileNotFoundException) {
                return $this->jsonRender(
                    'Requested file was not found.',
                    'ERROR_FILE_NOT_FOUND',
                    404,
                );
            }

            // Custom OAuth exceptions response
            if ($exception instanceof \Laravel\Passport\Exceptions\OAuthServerException && $exception->getPrevious() instanceof \League\OAuth2\Server\Exception\OAuthServerException) {
                switch ($exception->getPrevious()->getErrorType()) {
                    case 'invalid_credentials':
                    case 'invalid_grant':
                        return $this->jsonRender(
                            $exception->getPrevious()->getErrorType(),
                            'OAUTH_ERROR_INVALID_CREDENTIALS',
                            401
                        );
                    default:
                        return $this->jsonRender(
                            $exception->getPrevious()->getErrorType(),
                            'ERROR_SOMETHING_WRONG',
                            $exception->getPrevious()->getHttpStatusCode()
                        );
                }
            }

            // Generic response to other exceptions
            return $this->jsonRender(
                class_basename($exception),
                'ERROR_SOMETHING_WRONG',
                (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() !== null ? $exception->getStatusCode() : 500)
            );
        }

        return parent::render($request, $exception);
    }

    protected function jsonRender(string $error, $message, int $statusCode, bool $use_phrase = true) {
        return response()->json([
            'error'   => config('app.debug') ? $this->json_exception_message : $error,
            'message' => $use_phrase ? Phrase::getPhrase($message, 'api') : $message,
        ], $statusCode);
    }
}
