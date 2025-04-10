<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use View;

class ContactNotification extends Notification
{
    use Queueable;
    private $contact;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (isset($this->contact['email'])) {
            return ['mail'];
        } else {
            return ['mail'];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject= "Get In Touch On ".config('app.name');
        $mail_data=$this->contact;
        // $mailBodyContent = View::make('emails.contactNotification',['subject'=>$subject,'name' => 'Admin', 'gretting' => "Hello", 'mail_data' => $mail_data])->render();
        // dd($mailBodyContent);die;
        // // prepare array for storing data into log
        // $emailLogArray = array();
        // $emailLogArray['user_id'] = $this->appointment['dealer_id'];
        // $emailLogArray['subject'] = $this->appointment['mailSubject'];
        // $emailLogArray['email_body'] = $mailBodyContent;
        // $emailLogArray['created_at'] = date('Y-m-d H:i:s');
        // $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

        // // store the log into database
        // addEmailLog($emailLogArray);

        return (new MailMessage)
            ->view('emails.contactNotification',['subject'=>$subject,'name' => 'Admin', 'gretting' => "Hello", 'mail_data' => $mail_data])
            ->subject($subject);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    // public function toArray($notifiable)
    // {
    //     // return [
    //     //     'name' => $this->appointment['name'],
    //     //     'title' => $this->appointment['title'],
    //     //     'type' => $this->appointment['type'],
    //     //     'status' => $this->appointment['status'],
    //     //     'body' => $this->appointment['body'],
    //     //     'senderId' => $this->appointment['senderId'],
    //     //     'url' => $this->appointment['url'] . '?id=' . $this->appointment['id'],
    //     //     'id' => $this->appointment['id']
    //     // ];
    // }
}
