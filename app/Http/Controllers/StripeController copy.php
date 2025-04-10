<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Checkout\Session as stripeSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    // public function authorizeStripeAccount(Request $request){
    //     Stripe::setApiKey(env('STRIPE_SECRET'));

    //     $url = \Stripe\OAuth::authorizeUrl([
    //         'client_id' => 'ca_FLHyuckeRZdVh3yLHdIePccLeWYDi2uz',
    //         'response_type' => 'code',
    //         'scope' => 'read_write',
    //     ]);

    //     return redirect($url);
    // }
    public function authorizeStripeAccount(Request $request)
    {
        $loginUser=Auth::user();
        $user=User::find($loginUser->id);
        Stripe::setApiKey(config('services.stripe.secret'));
        $account_id= $user->stripe_account_id;
        if($account_id==''){
            $account=\Stripe\Account::create([
                'type' => 'standard',
                // 'type' => 'custom',
                'country' => 'US',
                'email' => $user->email,
                // 'business_type'=> 'company',
                // 'company'=>[
                //     'name'=>$user->company_name,
                //     'phone' => $user->phone,
                // ]
                // 'capabilities' => [
                //     'card_payments' => ['requested' => true],
                //     'transfers' => ['requested' => true],
                // ],
            ]);
            $account_id=$account->id;
            $user->stripe_account_id=$account_id;
            $user->stripe_account_status=4;
            $user->save();
        }
        if($account_id && $user->stripe_account_status!=1){
            $account_link = \Stripe\AccountLink::create(
                [
                    'account' => $account_id,
                    'refresh_url' => route('dealer.stripe.authorize-refresh'),
                    'return_url' => route('dealer.stripe.authorize-return'),
                    'type' => 'account_onboarding',
                ]
            );
            if($account_link->url){
                return redirect($account_link->url);
            }
        }
        Session::flash('error', "something went wrong!");
        return redirect()->route('dealer.profile');
    }
    public function authorize_refresh(Request $request){
        Session::flash('error', "timeout");
        return redirect()->route('dealer.profile');
    }
    public function authorize_return(Request $request)
    {
        // Session::flash('error', "something went wrong!");
        $loginUser = Auth::user();
        $user = User::find($loginUser->id);
        Stripe::setApiKey(config('services.stripe.secret'));

        $account=\Stripe\Account::retrieve(
            $user->stripe_account_id,
            []
        );
        // Check if the account is in the pending queue
        // echo  isset($account->pending_verification) && $account->pending_verification;
        $status = 4; // created
        if (isset($account->verification->disabled_reason) && $account->verification->disabled_reason == '') {            // $status = 5; // disabled
            $status = 2;
            Session::flash('success', "Your connection request has been submited, currently pending for varification in stripe");

        } elseif ((isset($account->charges_enabled) && $account->charges_enabled) || (isset($account->payouts_enabled) && $account->payouts_enabled)) {
            $status = 1; //active
            Session::flash('success', "Your stripe account has been connected successfully!");
        }elseif($account->details_submitted==1){
            $status=2;
        }
        $user->stripe_account_status=$status;
        $user->save();
        return redirect()->route('dealer.profile');
    }
    public function create_invoice(Request $request){


        Stripe::setApiKey(config('services.stripe.secret'));
        $subscriptionId= "sub_1MyXaMAHGaEkalbykn6ym2Fe";
        try {
            // Retrieve the subscription from Stripe
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            $invoice = \Stripe\Invoice::upcoming([
                'subscription' => $subscription->id,
            ]);
            pr(json_encode($invoice));die;
            // pr($subscription->quantity);die;
            // $retrive= $subscription->

            // pr($subscription->items->data[0]->id);die;
            // Update the subscription with the new quantity
            // $subscription->quantity = 25;
            // $subscription->prorate = false;
            // $subscription->save();

            // Update Usage Records
            $subscriptionItemId = $subscription->items->data[0]->id; // Replace with your subscription item ID
            // echo $subscriptionItemId;die;
            $quantity = 5; // Replace with the actual quantity used by the customer
            $timestamp = time(); // Replace with the actual timestamp of the usage

            // Create or update a usage record for the subscription item
            $usageRecord = \Stripe\SubscriptionItem::createUsageRecord(
                $subscriptionItemId,
                [
                    'quantity' => $quantity,
                    'timestamp' => $timestamp,
                    'action'=> 'set',
                ]
            );
            pr($usageRecord);die;
            // $invoice = \Stripe\Invoice::upcoming([
            //     'customer' => $subscription->customer,
            //     'subscription_proration_behavior' => 'create_prorations',
            //     'subscription' => $subscription->id,
            //     'subscription_items' => [
            //         [
            //             'id' => $subscription->items->data[0]->id,
            //             'deleted' => true,
            //             'clear_usage' => true
            //         ],
            //         // [
            //         //     'price' => 'price_1Mxq8gAHGaEkalbyJ8WyUYz4',
            //         //     'deleted' => false
            //         // ],
            //     ]
            // ]);
            // $invoice->pay();
            // pr($invoice);

            // Subscription quantity updated successfully
            // You can perform additional actions here if needed

        } catch (\Stripe\Exception\ApiErrorException $e) {
            pr($e->getMessage());
            die;
            // Handle any errors that may occur
            // You can customize error handling based on your application's requirements
            // For example, you can log the error or display an error message to the user
            // using $e->getMessage() or $e->getError()->message
        }
        pr($subscription);
        // $invoice = \Stripe\Invoice::create([
        //     'customer' => 'cus_NePhldwP1kIwET',
        //     'auto_advance' => true, // Automatically finalize the invoice
        //     'pending_invoice_items_behavior' => "exclude",
        //     'collection_method'=> 'send_invoice',
        //     'days_until_due'=>30,
        //     'currency'=>'EUR',
        // ]);
        // $invoiceItem=\Stripe\InvoiceItem::create([
        //     'customer' => "cus_NePhldwP1kIwET",
        //     'amount' => 16000,
        //     'currency' => 'EUR',
        //     'description' => 'Commission',
        //     'invoice'=> $invoice->id
        // ]);
        // $test=$invoice->pay();
        // $charge = \Stripe\Charge::create([
        //     'amount' => 1000, // The amount to charge in cents
        //     'currency' => 'USD', // The currency of the charge
        //     'source' => 'tok_visa', // The payment source (e.g., card token)
        //     // 'application_fee_amount' => 200, // The application fee in cents for the connected account
        // ], [
        //     'stripe_account' => 'acct_1MtnL0AclOiD3yc1', // The ID of the connected Stripe account
        // ]);

        // $charge = \Stripe\Charge::create([
        //     'amount' => 1000, // The amount to charge in cents
        //     'currency' => 'USD', // The currency of the charge
        //     // 'source' => 'tok_visa', // The payment source (e.g., card token)
        //     'source' => 'pm_1MwQL4AHGaEkalbyFLsQhVAp',
        //     'customer' => 'cus_Nhq1blEVmrIe0u',
        //     // 'payment_method' => 'pm_1MwNCGAHGaEkalby6XysrKZp',
        //     // 'application_fee_amount' => 200, // The application fee in cents for the connected account
        // ]);

        //  $charge = \Stripe\Charge::create([
        //     'amount' => 1000, // The amount to charge in cents
        //     'currency' => 'USD', // The currency of the charge
        //     // 'source' => 'tok_visa', // The payment source (e.g., card token)
        //     // 'source' => 'pm_1MwQL4AHGaEkalbyFLsQhVAp',
        //     'customer' => 'cus_Nhq1blEVmrIe0u',
        //     // 'payment_method' => 'pm_1MwNCGAHGaEkalby6XysrKZp',
        //     // 'application_fee_amount' => 200, // The application fee in cents for the connected account
        // ]);

        // // Handle the charge response
        // if ($charge->status === 'succeeded') {
        //     // Payment succeeded, handle accordingly
        // } else {
        //     // Payment failed, handle accordingly
        // }
        // pr($charge);die;

        $intent = \Stripe\PaymentIntent::create([
            'amount' => 2000,
            'currency' => 'usd',
            // 'automatic_payment_methods' => [
            //     'enabled' => true,
            // ],
            'customer' => "cus_NcqT2fBxmBk2Ne",
            'payment_method'=> 'pm_1MramSAHGaEkalby3Toy0DRO',
            'confirm'=>true
        ]);
        pr($intent);
    //    $result= $intent->capture();
    //     pr($result);die;
    }
}
