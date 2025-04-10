<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use View;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $subject = trans('translation.confirm_email_subject', ['name' => config('app.name')]);
            $body = trans('translation.confirm_email_body');
            $loginUser = Auth::user();
            $documents = [];

            $mailBodyContent = View::make('emails.general',['user' => $loginUser, 'subject' => $subject, 'body' => $body, $documents])->render();

            // prepare array for storing data into log
            $emailLogArray = array();
            $emailLogArray['user_id'] = $loginUser->id;
            $emailLogArray['subject'] = $subject;
            $emailLogArray['email_body'] = $mailBodyContent;
            $emailLogArray['created_at'] = date('Y-m-d H:i:s');
            $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

            // store the log into database
            addEmailLog($emailLogArray);

            return (new MailMessage)
                ->view("emails.general", ['user' => $loginUser, 'subject' => $subject, 'body' => $body, $documents])
                ->subject(trans('translation.confirm_email_subject', ['name' => config('app.name')]))
                // ->line(trans('translation.confirm_email_body', ['url' => $url]))
                ->action(trans('translation.confirm_email_action'), $url);
        });
    }
}
