<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Laravel\Horizon\Events\JobFailed;

class HorizonAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public JobFailed $event
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('⚠️ Ошибка выполнения задачи: ' . $this->event->job->resolveName())
            ->line("ID задачи: {$this->event->job->getJobId()}")
            ->line("Очередь: {$this->event->job->getQueue()}")
            ->line("Ошибка: {$this->event->exception->getMessage()}")
            ->action('Просмотреть ошибку', url('/horizon/failed'))
            ->line("Дата: " . now()->format('Y-m-d H:i:s'));
    }
}