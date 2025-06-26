<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        Horizon::routeMailNotificationsTo(env('HORIZON_ALERT_EMAIL', 'admin@example.com'));
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (User $user) {
            return true;
            // return $user &&  $user->role === 'admin';
        });
    }

    protected function authorization(): void
    {
        Horizon::auth(function ($request) {
            $user = $request->user();
            return $user && $user->role === 'admin';
        });
    }
}
