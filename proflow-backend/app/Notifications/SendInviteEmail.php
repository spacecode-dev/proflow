<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class SendInviteEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $user;
 

    public function __construct($user)
    {
        $this->user =  $user;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        
        $company=  $this->user->company()->select('companies.name','companies.workspace_url')->orderBy('companies.created_at', 'desc')->first();
        $subDomain=$company['workspace_url'];
        $mainDomain = config('app.frontend_main_domain');

        $url = "https://{$subDomain}.{$mainDomain}/sign-up";

        return ((new MailMessage)
                    ->subject('Proflow Email Invitation')
                    ->markdown(
                        'emails.email-invitation', ['singUpUrl' => $url,
                        'companyName'=> $subDomain,
                        'name' => $this->user->name
                        ]
                    ));
    }

    
}
