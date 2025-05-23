<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use App\Notifications\HorizonAlertNotification;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo();
        Horizon::routeMailNotificationsTo(env('HORIZON_ALERT_EMAIL', 'admin@example.com'));
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        // Horizon::notification(function ($notification) {
        //     return new HorizonAlertNotification($notification);
        // });
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
