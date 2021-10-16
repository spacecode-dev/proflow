<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueDueDateChanged extends Notification
{
    use Queueable;
    protected $issue, $company;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($issue, $company)
    {
        $this->issue = $issue;
        $this->company = $company;
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
        $mainDomain = config('app.frontend_main_domain');
        $url = "https://{$this->company}.{$mainDomain}/home/issue-view/{$this->issue->unique_id}";
        return (new MailMessage)->markdown(
                'emails.issue-due-date-changed', [
                'title' => 'The due date has changed!',
                'subLine2' => strip_tags($this->issue->title),
                'subLine1'  => "The times they are a-changin'... It looks like the due date <br/> has changed for the following issue:",
                'url' => $url,
                'buttonText' => 'View Issue'
                ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
