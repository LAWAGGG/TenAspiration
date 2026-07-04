<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KeluhKesahNotification extends Notification
{
    use Queueable;

    protected array $answers;
    protected array $questions;

    public function __construct(array $answers, array $questions)
    {
        $this->answers = $answers;
        $this->questions = $questions;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Keluh Kesah Baru Masuk! #' . uniqid())
            ->view('emails.keluhan', [
                'answers' => $this->answers,
                'questions' => $this->questions,
            ]);
    }
}
