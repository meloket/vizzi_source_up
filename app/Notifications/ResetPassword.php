<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class ResetPassword extends Notification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line(new HtmlString("Hello, <p>We wanted to reach out and let everyone know that we will be sending new log-in links and resetting the passwords for all poster presenters.</p><p> We know that there have been a few people having problems accessing the site so this will now allow you access. </p><p>If you have already completed your uploads, your original upload will be transferred over and you will not have to re-upload anything but we do recommend logging in with the new link just to make sure you are able to access the system. Please note that this Tuesday (09/22)  is the last day to upload or make any changes to your poster. </p><p>If you have any questions or if we can assist in any way please feel free to reach out to us at <a href='mailto:help@virtualcreativestudio.com'>help@virtualcreativestudio.com</a>. We have seen many wonderful posters be uploaded thus far and we can't wait to view them all at the conference!"))
            ->action('Reset Password', url(config('app.url').'/password/reset/'.$this->token).'?email='.urlencode($notifiable->email));
    }
}
