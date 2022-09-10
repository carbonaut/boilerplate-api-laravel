<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Policies discovery
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\Policies\\' . class_basename($modelClass) . 'Policy';
        });

        Passport::hashClientSecrets();
        Passport::tokensExpireIn((now()->addDays(1)));
        Passport::refreshTokensExpireIn((now()->addDays(30)));
    }
}
