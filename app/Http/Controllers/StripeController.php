<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Redirect the user to Stripe's OAuth authorization page.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorizeStripeAccount(Request $request)
    {
        try {
            $url = \Stripe\OAuth::authorizeUrl([
                'client_id' => config('services.stripe.client_id'),
                'response_type' => 'code',
                'scope' => 'read_write',
                'redirect_uri' => route('dealer.stripe.authorize-return'),
            ]);

            return redirect($url);
        } catch (ApiErrorException $e) {
            Log::error('Stripe OAuth Authorization Error: ' . $e->getMessage());
            Session::flash('error', 'Failed to initiate Stripe account authorization.');
            return redirect()->route('dealer.profile');
        }
    }

    /**
     * Handle the return from Stripe after OAuth authorization.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorize_return(Request $request)
    {
        $loginUser = Auth::user();
        $user = User::findOrFail($loginUser->id);

        try {
            $tokenResponse = $this->getToken($request->code);
            if (isset($tokenResponse['error'])) {
                throw new \Exception($tokenResponse['error']);
            }

            $connectedAccountId = $tokenResponse->stripe_user_id;
            $account = $this->getAccount($connectedAccountId);

            if (isset($account['error'])) {
                throw new \Exception($account['error']);
            }

            // Determine account status
            $status = $this->determineAccountStatus($account);

            // Update user with Stripe account details
            $user->stripe_account_status = $status;
            $user->stripe_account_id = $connectedAccountId;
            $user->save();

            // Set appropriate flash messages
            $this->setFlashMessageBasedOnStatus($status);
        } catch (\Exception $e) {
            Log::error('Stripe Account Authorization Error: ' . $e->getMessage());
            Session::flash('error', 'Failed to connect your Stripe account: ' . $e->getMessage());
        }

        return redirect()->route('dealer.profile');
    }

    /**
     * Retrieve the OAuth token from Stripe.
     *
     * @param string $code
     * @return object|array
     */
    private function getToken($code)
    {
        try {
            $token = \Stripe\OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);

            return $token;
        } catch (ApiErrorException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Retrieve the Stripe account details.
     *
     * @param string $connectedAccountId
     * @return object|array
     */
    private function getAccount($connectedAccountId)
    {
        try {
            $account = \Stripe\Account::retrieve($connectedAccountId, []);
            return $account;
        } catch (ApiErrorException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Determine the Stripe account status based on account details.
     *
     * @param object $account
     * @return int
     */
    private function determineAccountStatus($account)
    {
        if (isset($account->verification->disabled_reason) && empty($account->verification->disabled_reason)) {
            return 2; // Pending verification
        } elseif (($account->charges_enabled ?? false) || ($account->payouts_enabled ?? false)) {
            return 1; // Active
        } elseif (($account->details_submitted ?? false) == 1) {
            return 2; // Pending further details
        }

        return 4; // Created or other status
    }

    /**
     * Set flash messages based on account status.
     *
     * @param int $status
     * @return void
     */
    private function setFlashMessageBasedOnStatus($status)
    {
        switch ($status) {
            case 1:
                Session::flash('success', 'Your Stripe account has been connected successfully!');
                break;
            case 2:
                Session::flash('success', 'Your connection request has been submitted and is pending verification in Stripe.');
                break;
            default:
                Session::flash('error', 'Something went wrong while connecting your Stripe account.');
                break;
        }
    }

    /**
     * Create an invoice for a subscription.
     *
     * @param Request $request
     * @return void
     */
    public function create_invoice(Request $request)
    {


        Stripe::setApiKey(config('services.stripe.secret'));
        $subscriptionId = "sub_1MyXaMAHGaEkalbykn6ym2Fe";
        try {
            // Retrieve the subscription from Stripe
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            $invoice = \Stripe\Invoice::upcoming([
                'subscription' => $subscription->id,
            ]);
            pr(json_encode($invoice));
            die;
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
                    'action' => 'set',
                ]
            );
            pr($usageRecord);
            die;
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
            'payment_method' => 'pm_1MramSAHGaEkalby3Toy0DRO',
            'confirm' => true
        ]);
        pr($intent);
        //    $result= $intent->capture();
        //     pr($result);die;
    }
}
