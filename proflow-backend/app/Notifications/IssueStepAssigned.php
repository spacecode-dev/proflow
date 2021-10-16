<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueStepAssigned extends Notification
{
    use Queueable;
    protected $issue, $issueStep, $company, $user; 

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($issue, $issueStep, $company, $user)
    {
        $this->issue = $issue; 
        $this->company = $company; 
        $this->issueStep = $issueStep;
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
                'title' => strip_tags($this->user),
                'subTitle' => "has assigned you a next step:",
                'issueTitle' => strip_tags($this->issue->title),
                'url' => $url,
                'subLine1' => "You've been assigned a next step:",
                'subLine2'=> strip_tags($this->issueStep->text),
                'buttonText' => 'View Next Step'
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
