<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KeluhKesahNotification extends Notification
{
    use Queueable;

    protected $keluh;
    protected $phone;

    public function __construct($keluh, $phone)
    {
        $this->keluh = $keluh;
        $this->phone = $phone;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Keluh Kesah Baru Masuk! #' . uniqid())
            ->view('emails.keluhan', [
                'keluh' => $this->keluh,
                'phone' => $this->phone,
            ]);
    }
}
