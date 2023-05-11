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
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
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
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
        });

        // We'll register a renderable for all throwables, so we can rethrow it
        // using our translatable exception.
        $this->renderable(function (Throwable $e, $request) {
            if (!$request->expectsJson()) {
                return;
            }

            if ($e instanceof AuthenticationException) {
                throw new TranslatableException(
                    status: 401,
                    error: 'Unauthenticated.',
                    message: 'api.ERROR.AUTH.UNAUTHENTICATED',
                    previous: $e,
                    shouldReport: false
                );
            }

            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                throw new TranslatableException(
                    status: 403,
                    error: 'This action is unauthorized.',
                    message: 'api.ERROR.AUTH.UNAUTHORIZED',
                    previous: $e,
                    shouldReport: false
                );
            }

            if ($e instanceof ModelNotFoundException) {
                throw new TranslatableException(
                    status: 404,
                    error: $e->getMessage(),
                    message: 'api.ERROR.MODEL_NOT_FOUND',
                    previous: $e,
                    shouldReport: false
                );
            }

            if ($e instanceof NotFoundHttpException) {
                throw new TranslatableException(
                    status: $e->getStatusCode(),
                    error: $e->getMessage(),
                    message: 'api.ERROR.ROUTE_NOT_FOUND',
                    previous: $e,
                    shouldReport: false
                );
            }

            if ($e instanceof ValidationException) {
                throw new TranslatableException(
                    status: $e->status,
                    error: $e->errors(),
                    message: $e->getMessage(),
                    previous: $e,
                    messageTranslatable: false,
                    shouldReport: false,
                );
            }

            if ($e instanceof HttpException && App::isDownForMaintenance()) {
                throw new TranslatableException(
                    status: $e->getStatusCode(),
                    error: 'Application under maintenance.',
                    message: 'api.ERROR.MAINTENANCE',
                    previous: $e
                );
            }

            // Generic response to other exceptions, hiding important data when debug is disabled.
            $error = get_class($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();

            throw new TranslatableException(
                status: method_exists($e, 'getStatusCode') && $e->getStatusCode() !== null ? $e->getStatusCode() : 500,
                error: config('app.debug') && !App::runningUnitTests() ? $error : $e->getMessage(),
                message: 'api.ERROR.SOMETHING_WENT_WRONG',
                previous: $e
            );
        });
    }
}
