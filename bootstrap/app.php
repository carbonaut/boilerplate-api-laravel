<?php

use App\Exceptions\NormalizedException;
use App\Http\Middleware\BlockInProduction;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\Localize;
use App\Http\Middleware\ThrottleLogin;
use Bepsvpt\SecureHeaders\SecureHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
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
            // Exceptions will be rendered by the NormalizedException class.
            // If you don't want the exception to be reported, add it to the dontReport array.
            return NormalizedException::normalize($e)->render($request);
        });
    })->create()
;
