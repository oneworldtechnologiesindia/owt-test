<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Helpers\MailerFactory;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use View;

class PurchaseEnquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $purchaseEnquiry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($purchaseEnquiry)
    {
        $this->purchaseEnquiry = $purchaseEnquiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (isset($this->purchaseEnquiry['mailGretting']) && !empty($this->purchaseEnquiry['mailGretting']) && isset($this->purchaseEnquiry['mailBody']) && !empty($this->purchaseEnquiry['mailBody']) && isset($this->purchaseEnquiry['mailSubject']) && !empty($this->purchaseEnquiry['mailSubject'])) {
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
        $mailBodyContent = View::make('emails.generalNotification',['subject'=>$this->purchaseEnquiry['mailSubject'],'name' => $this->purchaseEnquiry['name'], 'gretting' => $this->purchaseEnquiry['mailGretting'], 'body' => $this->purchaseEnquiry['mailBody'], 'subject' => $this->purchaseEnquiry['mailSubject']])->render();
        
        // prepare array for storing data into log
        $emailLogArray = array();
        $emailLogArray['user_id'] = $this->purchaseEnquiry['dealer_id'];
        $emailLogArray['subject'] = $this->purchaseEnquiry['mailSubject'];
        $emailLogArray['email_body'] = $mailBodyContent;
        $emailLogArray['created_at'] = date('Y-m-d H:i:s');
        $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

        // store the log into database
        addEmailLog($emailLogArray);

        return (new MailMessage)
            ->view("emails.generalNotification", ['name' => $this->purchaseEnquiry['name'], 'gretting' => $this->purchaseEnquiry['mailGretting'], 'body' => $this->purchaseEnquiry['mailBody']])
            ->subject($this->purchaseEnquiry['mailSubject']);
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
            'name' => $this->purchaseEnquiry['name'],
            'title' => $this->purchaseEnquiry['title'],
            'type' => $this->purchaseEnquiry['type'],
            'status' => $this->purchaseEnquiry['status'],
            'body' => $this->purchaseEnquiry['body'],
            'senderId' => $this->purchaseEnquiry['senderId'],
            'url' => $this->purchaseEnquiry['url'] . '?oid=' . $this->purchaseEnquiry['id'],
            'id' => $this->purchaseEnquiry['id']
        ];
    }
}
