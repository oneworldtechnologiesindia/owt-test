<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use View;

class OrderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (isset($this->order['mailGretting']) && !empty($this->order['mailGretting']) && isset($this->order['mailBody']) && !empty($this->order['mailBody']) && isset($this->order['mailSubject']) && !empty($this->order['mailSubject'])) {
            if (isset($this->order['documents']) && !empty($this->order['documents']) && count($this->order['documents']) == 3) {
                return ['mail'];
            } else {
                return ['database', 'mail'];
            }
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
        $mailMessage = new MailMessage;
        if (isset($this->order['documents']) && !empty($this->order['documents']) && count($this->order['documents']) == 3) {
            $mailMessage->view("emails.generalNotification", ['name' => $this->order['name'], 'gretting' => $this->order['mailGretting'], 'body' => $this->order['mailBody']])
                ->subject($this->order['mailSubject']);


            $mailBodyContent = View::make('emails.generalNotification',['subject'=>$this->order['mailSubject'],'name' => $this->order['name'], 'gretting' => $this->order['mailGretting'], 'body' => $this->order['mailBody']])->render();

            // prepare array for storing data into log
            $emailLogArray = array();
            $emailLogArray['user_id'] = $this->order['dealer_id'];
            $emailLogArray['subject'] = $this->order['mailSubject'];
            $emailLogArray['email_body'] = $mailBodyContent;
            $emailLogArray['created_at'] = date('Y-m-d H:i:s');
            $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

            // store the log into database
            addEmailLog($emailLogArray);

            foreach ($this->order['documents'] as $document) {
                $mailMessage->attach($document['path'], [
                    'as' => $document['filename'],
                    'mime' => 'application/pdf'
                ]);
            }
        } else {
            $mailMessage->view("emails.generalNotification", ['name' => $this->order['name'], 'gretting' => $this->order['mailGretting'], 'body' => $this->order['mailBody']])
                ->subject($this->order['mailSubject']);
        }
        return $mailMessage;
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
            'name' => $this->order['name'],
            'title' => $this->order['title'],
            'type' => $this->order['type'],
            'status' => $this->order['status'],
            'body' => $this->order['body'],
            'senderId' => $this->order['senderId'],
            'url' => $this->order['url'] . '?oid=' . $this->order['id'],
            'id' => $this->order['id']
        ];
    }
}
