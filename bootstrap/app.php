<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            $domain = config('app.domain');
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
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create()
;
