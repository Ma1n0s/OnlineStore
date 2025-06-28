<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Mail;

use Illuminate\Support\Facades\Event;
use App\Events\OrderApiProcessed;
use App\Listeners\UpdateOrderFromApiResponse;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        Event::listen(
            OrderApiProcessed::class,
            UpdateOrderFromApiResponse::class
        );

        // Horizon::routeMailNotificationsTo(explode(',', env('HORIZON_ALERT_EMAILS', 'admin@example.com')));
        
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            
            // Добавьте ваши задачи здесь
            $schedule->command('guests:delete-expired')->daily();
        });

        Queue::failing(function (JobFailed $event) {

            $now = now()->format('Y-m-d H:i:s');
            $toEmail = env('HORIZON_ALERT_EMAILS', 'admin@example.com');

            $subject = '⚠️ Ошибка выполнения задачи';
            $message = 'Не прошел запрос на сервер 1с.'
                ."</br>ID задачи: {$event->job->getJobId()}"
                ."</br>Ошибка: {$event->exception->getMessage()}"
                ."</br>Дата $now";

            Mail::raw($message, function($msg) use ($toEmail, $subject) {
                $msg->to($toEmail)
                    ->subject($subject);
            });
        });
    }
}
