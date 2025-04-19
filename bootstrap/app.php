<?php

use App\Exceptions\TranslatableException;
use App\Http\Middleware\BlockInProduction;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\Localize;
use App\Http\Middleware\ThrottleLogin;
use Bepsvpt\SecureHeaders\SecureHeadersMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            $domain = Uri::of(config('app.url'))->host();
            assert(is_string($domain));

            Route::middleware(['api', 'throttle:api'])
                ->domain('api.' . $domain)
                ->group(base_path('routes/api.php'))
            ;

            Route::middleware('web')
                ->domain('www.' . $domain)
                ->group(base_path('routes/web.php'))
            ;

            Route::middleware('web')
                ->domain($domain)
                ->group(base_path('routes/web.php'))
            ;
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SecureHeadersMiddleware::class);
        $middleware->append(Localize::class);
        $middleware->preventRequestsDuringMaintenance(except: [
            'auth/login',
            'maintenance/up',
            'maintenance/down',
        ]);
        $middleware->trustProxies(
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_AWS_ELB
        );
        $middleware->alias([
            'block-in-production' => BlockInProduction::class,
            'verified'            => EnsureEmailIsVerified::class,
            'throttle_login'      => ThrottleLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportDuplicates();

        $exceptions->dontFlash([
            'current_password',
            'new_password',
            'password',
            'password_confirmation',
        ]);

        $exceptions->dontReport([
            // The following exceptions serve as examples and are not
            // reported by default as they extend HttpException, ignored by
            // $internalDontReport at Illuminate\Foundation\Exceptions\Handler.php
            MethodNotAllowedHttpException::class,
            NotFoundHttpException::class,
        ]);

        $exceptions->renderable(function (Throwable $e, $request) {
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
                    error: collect($e->errors())->flatten()->implode(' '),
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
                    previous: $e,
                    shouldReport: false,
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
    })->create()
;
