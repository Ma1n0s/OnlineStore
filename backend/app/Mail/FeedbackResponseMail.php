<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $response;
    public $originalMessage;

    public function __construct($subject, $response, $originalMessage)
    {
        $this->subject = $subject;
        $this->response = $response;
        $this->originalMessage = $originalMessage;
    }

    public function build()
    {
        return $this
            ->subject("Ответ на обращение: {$this->subject}")
            ->view('emails.feedback_response')
            ->with([
                'subject' => $this->subject,
                'responseContent' => $this->response,
                'originalMessage' => $this->originalMessage,
            ]);
    }
}