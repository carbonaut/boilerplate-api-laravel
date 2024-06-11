<?php

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
        $middleware->append(SecureHeadersMiddleware::class);
        $middleware->append(Localize::class);
        $middleware->preventRequestsDuringMaintenance(except: [
            'auth/login',
            'maintenance/up',
            'maintenance/down',
        ]);
        $middleware->trustProxies(
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB
        );
        $middleware->alias([
            'block-in-production' => BlockInProduction::class,
            'verified'            => EnsureEmailIsVerified::class,
            'throttle_login'      => ThrottleLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create()
;
