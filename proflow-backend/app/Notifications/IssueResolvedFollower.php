<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueResolvedFollower extends Notification
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
                'emails.issue-view-resolve', [
                'title' => strip_tags($this->issue->title),
                'subTitle' => "has been resovled in ProFlow!",
                'url' => $url,
                'subLine1' => "Nice one - it looks like your team has now resolved the following issue:",
                'subLine2'=> strip_tags($this->issue->title),
                'subLine3'=> "Well done on helping to keep your team moving forward. You can review how it was resolved and the outcome below",
                'buttonText' => 'View Resolved Issue'
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
