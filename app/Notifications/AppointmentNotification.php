<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use View;

class AppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (isset($this->appointment['mailGretting']) && !empty($this->appointment['mailGretting']) && isset($this->appointment['mailBody']) && !empty($this->appointment['mailBody']) && isset($this->appointment['mailSubject']) && !empty($this->appointment['mailSubject'])) {
            return ['database', 'mail'];
        } else {
            return ['database'];
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
        $mailBodyContent = View::make('emails.generalNotification',['subject'=>$this->appointment['mailSubject'],'name' => $this->appointment['name'], 'gretting' => $this->appointment['mailGretting'], 'body' => $this->appointment['mailBody']])->render();

        // prepare array for storing data into log
        $emailLogArray = array();
        $emailLogArray['user_id'] = $this->appointment['dealer_id'];
        $emailLogArray['subject'] = $this->appointment['mailSubject'];
        $emailLogArray['email_body'] = $mailBodyContent;
        $emailLogArray['created_at'] = date('Y-m-d H:i:s');
        $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

        // store the log into database
        addEmailLog($emailLogArray);

        return (new MailMessage)
            ->view("emails.generalNotification", ['name' => $this->appointment['name'], 'gretting' => $this->appointment['mailGretting'], 'body' => $this->appointment['mailBody']])
            ->subject($this->appointment['mailSubject']);
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
            'name' => $this->appointment['name'],
            'title' => $this->appointment['title'],
            'type' => $this->appointment['type'],
            'status' => $this->appointment['status'],
            'body' => $this->appointment['body'],
            'senderId' => $this->appointment['senderId'],
            'url' => $this->appointment['url'] . '?id=' . $this->appointment['id'],
            'id' => $this->appointment['id']
        ];
    }
}
