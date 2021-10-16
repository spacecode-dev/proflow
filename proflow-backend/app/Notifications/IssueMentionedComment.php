<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueMentionedComment extends Notification
{
    use Queueable;
    protected $issue, $company, $body, $user; 

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($issue, $company, $body, $user)
    {
        $this->issue = $issue;
        $this->company = $company;
        $this->body = $body;
        $this->user = $user;
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
                'emails.issue-view', [
                'title' => $this->user,
                'subTitle' => "has mentioned you in a comment",
                'issueTitle' =>  strip_tags($this->issue->title),
                'url' => $url,
                'subLine1' => "You've been mentioned in a next comment",
                'commentSubLine'=> $this->body,
                'buttonText' => 'View Comment'
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
