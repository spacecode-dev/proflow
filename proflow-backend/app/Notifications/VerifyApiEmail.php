<?php

namespace App\Notifications;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyApiEmail extends VerifyEmailBase
    {


    /**
    * Get the verification URL for the given notifiable.
    *
    * @param mixed $notifiable
    * @return string
    */
    protected function verificationUrl($notifiable)
    {

        VerifyApiEmail::toMailUsing(function ($notifiable){

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]);
                return ((new MailMessage)
                    ->subject('Proflow Email Registration')
                    ->markdown(
                        'emails.verify-email', ['verifyUrl' => $verifyUrl,
                        'message' => $this]
                        
                    ));

        });

       
    }
    
    }

