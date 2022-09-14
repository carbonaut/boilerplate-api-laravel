<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
    }
}
