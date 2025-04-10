<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use \App\Models\Packages;
use Stripe\Stripe;
use App\Models\SubscriptionLog;
use Illuminate\Support\Facades\Log;
use Stripe\Subscription;
use \App\Models\Order;
use Exception;
use PDF;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    public function stripeSubscription(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Signature');

        $secret = config('services.stripe.secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        // if ($signature !== $expectedSignature) {
        //     return response('Invalid signature', 401);
        // }

        // handle the subscription event
        $event = json_decode($payload, true);
        Log::channel('webhook_log')->info('Webhook received', ['payload' => $event]);
        switch ($event['type']) {
            case 'invoice.payment_succeeded':
                $eventData = $event['data']['object'];

                // perform any necessary actions for the created subscription
                if ($eventData['billing_reason'] == 'subscription_cycle') {
                    $user = User::where('subscription_id', $eventData['subscription'])->first();

                    if ($user) {
                        Stripe::setApiKey(config('services.stripe.secret'));
                        $package = Packages::find($user->package_id);
                        $subscription = \Stripe\Subscription::retrieve($eventData['subscription']);
                        $trasaction = SubscriptionLog::where(['subscription_id' => $eventData['subscription'], 'invoice_id' => $eventData['id']])->first();
                        $orders = Order::where(['subscription_id' => $subscription->id, 'is_deducted_commission' => 0])->get();

                        if (!empty($subscription) && $subscription->status == "active") {
                            $s_log = new SubscriptionLog;
                            $s_log->inv_number = getInvNumber($user);
                            $s_log->package_id = $package->id;
                            $s_log->user_id = $user->id;
                            $s_log->subscription_id = $eventData['subscription'];
                            $s_log->invoice_id = $eventData['id'];
                            $s_log->subscription_date = $user->subscription_date;
                            $s_log->sub_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
                            $s_log->sub_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
                            $s_log->status = 1;
                            $s_log->amount = ($eventData['amount_paid']) / 100;
                            $s_log->tax = ($eventData['total'] - $eventData['total_excluding_tax']) / 100;
                            $s_log->amount_total = ($eventData['total']) / 100;
                            $s_log->response = json_encode($eventData);
                            if ($eventData['id']) {
                                $invoice = \Stripe\Invoice::retrieve($eventData['id']);
                                if (!empty($invoice)) {
                                    $s_log->transaction_id = $invoice->charge;
                                }
                            }
                            $s_log->created_at = date('Y-m-d H:i:s');
                            $s_log->save();

                            $user->package_id = $package->id;
                            $user->customer_id = $subscription->customer;
                            $user->subscription_id = $subscription->id;
                            $user->sub_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
                            $user->sub_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
                            $user->is_active_subscription = 1;
                            $user->save();
                            // create invoice and save it.
                            // $invoice_public_path = 'public/subscription_invoice';
                            // $invoice_absoulute_path = 'app/' . $invoice_public_path;

                            // if (!file_exists(storage_path($invoice_absoulute_path))) {

                            //     Storage::makeDirectory($invoice_public_path);
                            // }
                            // $dealer = $user;
                            // header('Content-Type: text/html; charset=utf-8');
                            // $invoice_pdf = PDF::loadView('subscription.invoice.invoice', compact('orders', 's_log', 'package', 'dealer'));

                            // $invoice_pdf_filename = trans('translation.Invoice') . '-' . $s_log->inv_number . '.pdf';
                            // $invoice_pdf_path = storage_path($invoice_absoulute_path . '/' . $invoice_pdf_filename);
                            // $invoice_pdf->save($invoice_pdf_path);
                            // end of saving invoice
                            try {
                                $subscription->quantity = 1;
                                $subscription->prorate = false;
                                $subscription->save();
                            } catch (Exception $e) {
                                Log::channel('webhook_log')->warning("not able to update subscription on webhook " . $subscription->id);
                            }
                        } else {
                            if ($subscription->status !== "active") {
                                Log::channel('webhook_log')->warning("subscription status getting not active");
                            }
                            if ($subscription) {
                                Log::channel('webhook_log')->warning("trasaction already stored, may be duplicate");
                            }
                        }
                        try {
                            Order::where(['subscription_id' => $subscription->id, 'is_deducted_commission' => 0])->update(['is_deducted_commission' => 1, 'commision_invoice_id' => $eventData['id']]);
                        } catch (Exception $e) {
                            Log::channel('webhook_log')->warning("Can not able to update order USERID: " . $user->id);
                        }
                        // $old_subscriptoin_trans = SubscriptionLog::where(['subscription_id' => $eventData['subscription']])->->first();

                        // here we have to deduct commision base.
                        // if(!empty($user->customer_id)){
                        //     $customer_id=$user->customer_id;
                        // }
                        // else if (!empty($subscription->customer_id)){
                        //     $customer_id= $subscription->customer_id;
                        // }
                        // $customer_id = $subscription->customer;
                        // pr($customer_id);die;
                        // echo $customer_id;die;
                        // if($customer_id){
                        //     // need to create charge for commision
                        //     // $orders= Order::where(['orders.dealer_id'=>$user->id,'is_deducted_commission'=>0])->get();
                        //     $orders = Order::where(['is_deducted_commission' => 0])->get();
                        //     if(!empty($orders)){
                        //         $total_deduction_amount=$orders->sum('site_commission');
                        //         // pr($total_deduction_amount);die;
                        //         if($total_deduction_amount>0){
                        //             try{
                        //                 $stripe_charge_amount = round($total_deduction_amount,2) * 100;
                        //                 $customer = \Stripe\Customer::retrieve($customer_id);

                        //                 $invoice = new \Stripe\Invoice();

                        //                 // Set the invoice details
                        //                 $invoice->customer_id = $customer->id; // assuming customer_id is the foreign key in Invoice model
                        //                 $invoice->date = now(); // or you can set the date based on your requirements
                        //                 $invoice->invoice_number = 'INV' . rand(1000, 9999); // or generate invoice number based on your business logic
                        //                 // $invoice->items()->create([
                        //                 //     'description' => $description,
                        //                 //     'quantity' => $quantity,
                        //                 //     'unit_price' => $unitPrice,
                        //                 //     'total' => $total,
                        //                 // ]);
                        //                 // pr($customer);die;
                        //                 // $charge = \Stripe\Charge::create([
                        //                 //     'amount' => $stripe_charge_amount,
                        //                 //     'currency' => 'eur',
                        //                 //     'source' => "pm_1MwNCGAHGaEkalby6XysrKZp",
                        //                 //     'description' =>'monthly commision deducted in HIFIQUEST',
                        //                 // ]);

                        //                $intent= \Stripe\PaymentIntent::create([
                        //                     'amount' => 2000,
                        //                     'currency' => 'usd',
                        //                     'automatic_payment_methods' => [
                        //                         'enabled' => true,
                        //                     ],
                        //                     'customer'=> $customer_id
                        //                 ]);
                        //                 $charge = \Stripe\Charge::create([
                        //                     'amount' => $stripe_charge_amount,
                        //                     'currency' => 'eur',
                        //                     'source' => $intent->id,
                        //                     'description' => 'monthly commision deducted in HIFIQUEST',
                        //                 ]);

                        //                 // $charge=\Stripe\Charge::create([
                        //                 //     'amount' => $stripe_charge_amount,
                        //                 //     'currency' => 'eur',
                        //                 //     'customer' => $customer_id,
                        //                 //     'description' => 'monthly commision deducted in HIFIQUEST',
                        //                 // ]);
                        //             } catch (\Stripe\Exception\CardException $e) {
                        //                 $result = ['status' => false, 'message' => $e->getMessage()];
                        //                 Log::channel('webhook_log')->error($result);
                        //             } catch (\Stripe\Exception\RateLimitException $e) {
                        //                 // Too many requests made to the API too quickly
                        //                 $result = ['status' => false, 'message' => $e->getMessage()];
                        //                 Log::channel('webhook_log')->error($result);
                        //             } catch (\Stripe\Exception\InvalidRequestException $e) {
                        //                 // Invalid parameters were supplied to Stripe's API
                        //                 $result = ['status' => false, 'message' => $e->getMessage()];
                        //                 Log::channel('webhook_log')->error($result);
                        //             } catch (\Stripe\Exception\AuthenticationException $e) {
                        //                 // Authentication with Stripe's API failed
                        //                 // (maybe you changed API keys recently)
                        //                 $result = ['status' => false, 'message' => $e->getMessage()];
                        //                 Log::channel('webhook_log')->error($result);
                        //             } catch (\Stripe\Exception\ApiConnectionException $e) {
                        //                 // Network communication with Stripe failed
                        //                 $result = ['status' => false, 'message' => $e->getMessage()];
                        //                 Log::channel('webhook_log')->error($result);
                        //             } catch (\Stripe\Exception\ApiErrorException $e) {
                        //                 // Display a very generic error to the user, and maybe send
                        //                 // yourself an email
                        //                 $result = ['status' => false, 'message' => $e->getMessage()];
                        //                 Log::channel('webhook_log')->error($result);
                        //             } catch (Exception $e) {
                        //                 // Something else happened, completely unrelated to Stripe
                        //                 $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                        //                 Log::channel('webhook_log')->error($result);
                        //             }
                        //             pr($charge);die;
                        //             if(!empty($charge->id)){
                        //                 if($charge->paid==true){

                        //                 }
                        //                 pr($charge);die;
                        //             }
                        //         }
                        //     }
                        // }
                        // // end
                    } else {
                        Log::channel('webhook_log')->warning("User Not found!");
                    }
                } else {
                    Log::channel('webhook_log')->warning("Invalid billing cycle we need subscription_cycle ");
                }
                break;
            case 'customer.subscription.created':
                $subscriptionEvent = $event['data']['object'];
                // perform any necessary actions for the created subscription
                break;
            case 'customer.subscription.updated':
                $subscriptionEvent = $event['data']['object'];

                // perform any necessary actions for the updated subscription
                break;
            case 'customer.subscription.deleted':
                $subscriptionEvent = $event['data']['object'];
                $user = User::where('subscription_id', $subscriptionEvent['id'])->first();
                if ($user) {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $package = Packages::find($user->package_id);
                    $subscription = \Stripe\Subscription::retrieve($subscriptionEvent['id']);
                    if (!empty($subscription) && $subscription->status == "canceled") {
                        $user->subscription_id = NULL;
                        $user->sub_period_start = NULL;
                        $user->sub_period_end = NULL;
                        $user->subscription_date = NULL;
                        $user->is_active_subscription = 0;
                        // $user->is_subscription_canceled = 0;
                        $user->save();
                    }
                } else {
                    Log::channel('webhook_log')->warning("User Not found!");
                }
                break;
            default:
                // handle other events, if necessary
                break;
        }
        return response('Webhook received', 200);
    }
    public function accountWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Signature');

        $secret = config('services.stripe.secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        // if ($signature !== $expectedSignature) {
        //     return response('Invalid signature', 401);
        // }

        // handle the subscription event
        $event = json_decode($payload);
        Log::channel('webhook_log')->info('Webhook received', ['payload' => $event]);

        switch ($event->type) {
            case 'account.updated':
                $account = $event->data->object;
                $account_id = $account->id;
                Stripe::setApiKey(config('services.stripe.secret'));
                $accountDetails = \Stripe\Account::retrieve($account_id);
                if ($accountDetails) {
                    if ($accountDetails->charges_enabled == 1 && $accountDetails->payouts_enabled == 1) {
                        $user = User::find($account_id);
                        $user->account_verification_status = 1;
                        $user->save();
                    }
                }
            case 'account.application.authorized':
                $application = $event->data->object;
            case 'account.application.deauthorized':
                $application = $event->data->object;
            case 'account.external_account.created':
                $externalAccount = $event->data->object;
            case 'account.external_account.deleted':
                $externalAccount = $event->data->object;
            case 'account.external_account.updated':
                $externalAccount = $event->data->object;
                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }
    }
}
