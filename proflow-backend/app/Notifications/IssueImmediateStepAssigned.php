<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueImmediateStepAssigned extends Notification
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
                'title' => "You're in charge of the immediate next step! in Updating UX Processes",
                 'issueTitle' => strip_tags($this->issue->title),
                 'url' => $url,
                 'subLine1' => "It looks like the balls in your court and the team is waiting on you to complete the next step: Jack to finalize onboarding documents",
                 'subLine2'=> strip_tags($this->issueStep->text),
                 'footerType'=> 1,
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
