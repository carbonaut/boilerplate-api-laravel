<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Validation\ValidationException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'new_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });

        // We'll register a renderable for all throwables, so we can rethrow it
        // using our standardized exception.
        $this->renderable(function (Throwable $e, $request) {
            if (!$request->expectsJson()) {
                return;
            }

            if ($e instanceof AuthenticationException) {
                throw new StandardException(
                    401,
                    'Unauthenticated.',
                    __('api.ERROR.AUTH.UNAUTHENTICATED'),
                    $e
                );
            }

            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                throw new StandardException(
                    403,
                    'This action is unauthorized.',
                    __('api.ERROR.AUTH.UNAUTHORIZED'),
                    $e
                );
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                throw new StandardException(
                    404,
                    'Object not found (' . $e->getMessage() . ')',
                    __('api.ERROR.MODEL_NOT_FOUND'),
                    $e
                );
            }

            if ($e instanceof ValidationException) {
                throw new StandardException(
                    $e->status,
                    collect($e->errors())->flatten()->implode(' '),
                    (string) collect($e->errors())->flatten()->first(),
                    $e
                );
            }

            if ($e instanceof HttpException && App::isDownForMaintenance()) {
                throw new StandardException(
                    $e->getStatusCode(),
                    'Application under maintenance.',
                    __('api.ERROR.MAINTENANCE'),
                    $e
                );
            }

            // Generic response to other exceptions, hiding important data when debug is disabled.
            $error = get_class($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();

            throw new StandardException(
                method_exists($e, 'getStatusCode') && $e->getStatusCode() !== null ? $e->getStatusCode() : 500,
                config('app.debug') && !App::runningUnitTests() ? $error : 'Server Error',
                __('api.ERROR.SOMETHING_WENT_WRONG'),
                $e
            );
        });
    }
}
