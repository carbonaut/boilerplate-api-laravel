<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            /** @var null|User */
            $user = $request->user();

            return Limit::perMinute(60)->by($user?->id ?: $request->ip());
        });
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $domain = strval(config('app.domain'));

            Route::middleware('api')
                ->domain('api.' . $domain)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->domain('www.' . $domain)
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->domain($domain)
                ->group(base_path('routes/web.php'));
        });
    }
}
