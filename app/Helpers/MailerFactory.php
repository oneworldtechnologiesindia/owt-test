<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Mail\Mailer;
use App\User;
use View;

class MailerFactory
{
    protected $mailer;
    protected $fromAddress = "";
    protected $fromName = "";

    /**
     * MailerFactory constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->fromAddress = env('MAIL_FROM_ADDRESS');
        $this->fromName = env('MAIL_FROM_NAME');
    }
    /**
     * sendWelcomeEmail
     *
     *
     * @param $subject
     * @param $user
     */
    public function sendWelcomeEmail($user)
    {
        return true;
        $subject = "Thank you for registering";
        try {

            $mailBodyContent = View::make('emails.welcome',['user' => $user, 'subject' => $subject])->render();

            // prepare array for storing data into log
            $emailLogArray = array();
            $emailLogArray['user_id'] = $user->id;
            $emailLogArray['subject'] = $subject;
            $emailLogArray['email_body'] = $mailBodyContent;
            $emailLogArray['created_at'] = date('Y-m-d H:i:s');
            $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

            // store the log into database
            addEmailLog($emailLogArray);

            $this->mailer->send("emails.welcome", ['user' => $user, 'subject' => $subject], function ($message) use ($subject, $user) {

                $message->from($this->fromAddress, $this->fromName)
                    ->to($user->email)->subject($subject);
            });
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }

    public function sendGeneralEmail($user, $subject, $body = '', $documents = [])
    {
        try {

            $mailBodyContent = View::make('emails.general',['user' => $user, 'subject' => $subject, 'body' => $body])->render();

            // prepare array for storing data into log
            $emailLogArray = array();
            $emailLogArray['user_id'] = $user->id;
            $emailLogArray['subject'] = $subject;
            $emailLogArray['email_body'] = $mailBodyContent;
            $emailLogArray['created_at'] = date('Y-m-d H:i:s');
            $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

            // store the log into database
            addEmailLog($emailLogArray);

            $this->mailer->send("emails.general", ['user' => $user, 'subject' => $subject, 'body' => $body], function ($message) use ($subject, $user, $documents) {

                $message->from($this->fromAddress, $this->fromName)
                    ->to($user->email)->subject($subject);

                if (isset($documents) && !empty($documents)) {
                    foreach ($documents as $document) {
                        $message->attach($document['path'], [
                            'as' => $document['filename'],
                            'mime' => 'application/pdf'
                        ]);
                    }
                }
            });
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }

    public function sendNotificationEmail($name, $email, $gretting = '', $subject, $body = '', $documents = [])
    {
        try {

            $mailBodyContent = View::make('emails.general',['user' => $user, 'subject' => $subject, 'body' => $body])->render();

            // prepare array for storing data into log
            $emailLogArray = array();
            $emailLogArray['user_id'] = $user->id;
            $emailLogArray['subject'] = $subject;
            $emailLogArray['email_body'] = $mailBodyContent;
            $emailLogArray['created_at'] = date('Y-m-d H:i:s');
            $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

            // store the log into database
            addEmailLog($emailLogArray);

            $this->mailer->send("emails.general", ['name' => $name, 'subject' => $subject, 'body' => $body], function ($message) use ($subject, $name, $documents, $email) {

                $message->from($this->fromAddress, $this->fromName)
                    ->to($email)->subject($subject);

                if (isset($documents) && !empty($documents)) {
                    foreach ($documents as $document) {
                        $message->attach($document['path'], [
                            'as' => $document['filename'],
                            'mime' => 'application/pdf'
                        ]);
                    }
                }
            });
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }

    public function sendEnquiryToDealerEmail($purchaseEnquiry, $allDealer, $loginUser)
    {
        $subject = "New Purchase Enquiry";
        try {
            foreach ($allDealer as $key => $data) {
                $mailBodyContent = View::make('emails.purchase_enquiry',['purchaseEnquiry' => $purchaseEnquiry, 'data' => $data, 'loginUser' => $loginUser, 'subject' => $subject])->render();

                // prepare array for storing data into log
                $emailLogArray = array();
                //$emailLogArray['user_id'] = $user->id;
                $emailLogArray['subject'] = $subject;
                $emailLogArray['email_body'] = $mailBodyContent;
                $emailLogArray['created_at'] = date('Y-m-d H:i:s');
                $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

                // store the log into database
                //addEmailLog($emailLogArray);

                $this->mailer->send("emails.purchase_enquiry", ['purchaseEnquiry' => $purchaseEnquiry, 'data' => $data, 'loginUser' => $loginUser, 'subject' => $subject], function ($message) use ($subject, $data) {

                    $message->from($this->fromAddress, $this->fromName)
                        ->to($data['email'])->subject($subject);
                });
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
        return true;
    }

    public function sendDealerStatusLevelUpdateEmail($dealer, $changeStatus)
    {
        $statulevels = User::$statulevels;
        $userPercentage = User::$userPercentage;
        if ($dealer->status_level != $changeStatus && isset($statulevels[$changeStatus]) && isset($userPercentage[$changeStatus])) {
            $subject = trans('translation.dealerlevel_email_subject');
            $type = 'normal';
            if ($dealer->status_level < $changeStatus) {
                $body = trans('translation.dealerlevel_upgrade_email_body', ['level' => $statulevels[$changeStatus], 'percentage' => $userPercentage[$changeStatus]]);
                $type = 'upgraded';
            } else {
                $body = trans('translation.dealerlevel_downgrade_email_body', ['level' => $statulevels[$changeStatus]]);
                $type = 'downgraded';
            }

            if (isset($type) && !empty($type) && $type != 'normal') {
                try {

                    // prepare array for storing data into log
                    $emailLogArray = array();
                    $emailLogArray['user_id'] = $dealer->id;
                    $emailLogArray['subject'] = $subject;
                    $emailLogArray['email_body'] = $body;
                    $emailLogArray['created_at'] = date('Y-m-d H:i:s');
                    $emailLogArray['updated_at'] = date('Y-m-d H:i:s');

                    // store the log into database
                    addEmailLog($emailLogArray);

                    $this->mailer->send("emails.dealer_status", ['dealer' => $dealer, 'subject' => $subject, 'type' => $type, 'body' => $body], function ($message) use ($subject, $dealer) {

                        $message->from($this->fromAddress, $this->fromName)
                            ->to($dealer->email)->subject($subject);
                    });
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }
                return true;
            }
            return false;
        } else {
            return false;
        }
    }
}
