<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ForgetPassword extends Notification
{
    use Queueable;
    public $user;
    public $token;
    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, $token, $password)
    {
        $this->user = $user;
        $this->token = $token;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Forget Password | Sistem Informasi Siswa')
            ->line("Apa kabar, {$this->user->name}")
            ->line("Anda berhasil menerima pesan ini untuk mengubah password, silahkan gunakan password dibawah ini untuk melakukan login akun!")
            ->line("Username : {$this->user->username}")
            ->line(new HtmlString("<center><strong><h3>Password</h3></strong></center>"))
            ->line(new HtmlString("<center><h1><strong>{$this->password}</strong></h1></center>"))
            ->line('Terima Kasih!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
