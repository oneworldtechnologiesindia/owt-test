<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Packages;
use App\Models\SubscriptionLog;
use Stripe\Stripe;
use Stripe\Checkout\Session as stripeSession;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Stripe\Subscription;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $loginUser = Auth::user();
        if ($loginUser->role_type == 2) {
            $getDealerCurrencyType = getDealerCurrencyType($loginUser);
            $currency_type = 1;
            if ($getDealerCurrencyType == "usd") {
                $currency_type = 2;
            }
            $packages = Packages::where('plan_currency', $currency_type)->get();
            return view('subscription.index', compact('packages', 'loginUser'));
        } else {
            return abort('404');
        }
    }
    public function processSubscription(Request $request)
    {
        $loginUser = Auth::user();
        $getDealerCurrencyType = getDealerCurrencyType($loginUser);
        $payment_method_types = ['card', 'sepa_debit'];
        $currency_type = 1;
        if ($getDealerCurrencyType == "usd") {
            $currency_type = 2;
            $payment_method_types = ['card'];
        }
        $data = ['status' => false, 'message' => trans('translation.Something went wrong')]; // default return
        Stripe::setApiKey(config('services.stripe.secret'));
        if (!empty($request->package_id)) {
            $package = Packages::find($request->package_id);
            if ($loginUser->customer_id) {
                try {
                    $session = stripeSession::create([
                        'customer' => $loginUser->customer_id,
                        'customer_update' => [
                            'address' => 'auto',
                        ],
                        'payment_method_types' => $payment_method_types,
                        // 'line_items' => [[
                        //     'price' => $package->stripe_package_id,
                        //     'quantity' => 1,
                        // ]],
                        'subscription_data' => [
                            'items' => [
                                [
                                    'plan' => $package->stripe_package_id, // The ID of the plan you created earlier
                                ],
                            ],
                        ],
                        'mode' => 'subscription',
                        'success_url' => route('dealer.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => route('dealer.checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                        'automatic_tax' => [
                            'enabled' => true,
                        ],
                    ]);
                } catch (\Stripe\Exception\CardException $e) {
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\RateLimitException $e) {
                    // Too many requests made to the API too quickly
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    // Authentication with Stripe's API failed
                    // (maybe you changed API keys recently)
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    // Network communication with Stripe failed
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                    return response()->json($result);
                }
            } else {
                // create new subscription through checkout page
                try {
                    $session = stripeSession::create([
                        'payment_method_types' => $payment_method_types,
                        'automatic_tax' => [
                            'enabled' => true,
                        ],
                        'subscription_data' => [
                            'items' => [
                                [
                                    'plan' => $package->stripe_package_id, // The ID of the plan you created earlier
                                ],
                            ],
                        ],
                        'customer_email' => $loginUser->email,
                        'mode' => 'subscription', // subscription - setup - payment
                        'success_url' => route('dealer.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => route('dealer.checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                    ]);
                } catch (\Stripe\Exception\CardException $e) {
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\RateLimitException $e) {
                    // Too many requests made to the API too quickly
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Invalid parameters were supplied to Stripe's API
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    // Authentication with Stripe's API failed
                    // (maybe you changed API keys recently)
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    // Network communication with Stripe failed
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Display a very generic error to the user, and maybe send
                    // yourself an email
                    $result = ['status' => false, 'message' => $e->getMessage()];
                    return response()->json($result);
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                    return response()->json($result);
                }
            }
            if (!empty($session->url)) {
                $data = ['status' => true, 'redirect_url' => $session->url];
            }
        }

        return response()->json($data, 200);
        // return redirect($session->url);
    }


    public function success(Request $request)
    {
        $loginUser = Auth::user();
        $user = User::find($loginUser->id);
        $error_message = '';
        if (!empty($request->session_id) && $user) {
            // Retrieve the Checkout session ID from the query string
            $sessionId = $request->input('session_id');
            $subscriptionlog = SubscriptionLog::where('session_id', $sessionId)->first();
            // if (!empty($subscriptionlog->id)) {
            //     return redirect()->route('subscription');
            // }
            Stripe::setApiKey(config('services.stripe.secret'));
            // Retrieve the Checkout session object from Stripe
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            // it is use when checkout with setup
            // $setupintent=\Stripe\SetupIntent::retrieve($session->setup_intent,[]);
            // // pr($setupintent);die;
            // $customer=\Stripe\Customer::Create([
            //     'description' => 'My First Test Customer',
            //     'email'=>$loginUser->email
            // ]);
            // // add payment method into customer
            // $pm = \Stripe\PaymentMethod::retrieve($setupintent->payment_method);
            // // $pm->attach(['customer' => $customer->id]);

            // // pr($pm);die;

            // $customer=\Stripe\Customer::retrieve($customer->id);
            // pr($customer);die;
            $stripe_package_id = $session->display_items[0]->plan->id;
            $package = Packages::where('stripe_package_id', $stripe_package_id)->first();
            $old_subscription_id = $user->subscription_id;
            if (!empty($session->subscription)) {
                $subscription = \Stripe\Subscription::retrieve($session->subscription);
                if (!empty($subscription) && $subscription->status == "active") {
                    $s_log = new SubscriptionLog;
                    $s_log->inv_number = getInvNumber($user);
                    $s_log->package_id = $package->id;
                    $s_log->user_id = $loginUser->id;
                    $s_log->subscription_id = $session->subscription;
                    $s_log->invoice_id = $session->invoice;
                    $s_log->subscription_date = date('Y-m-d H:i:s');
                    $s_log->sub_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
                    $s_log->sub_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
                    $s_log->status = 1;
                    $s_log->amount = ($session->amount_subtotal) / 100;
                    $s_log->tax = ($session->amount_total - $session->amount_subtotal) / 100;
                    $s_log->amount_total = ($session->amount_total) / 100;
                    $s_log->response = json_encode($session);
                    $s_log->session_id = $sessionId;
                    if ($session->invoice) {
                        $invoice = \Stripe\Invoice::retrieve($session->invoice);
                        if (!empty($invoice)) {
                            $s_log->transaction_id = $invoice->charge;
                        }
                    }
                    $s_log->created_at = date('Y-m-d H:i:s');
                    $s_log->save();

                    // // create invoice and save it.
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

                    // store data to user table
                    $user->package_id = $package->id;
                    $user->customer_id = $subscription->customer;
                    $user->subscription_id = $subscription->id;
                    $user->subscription_date = date('Y-m-d H:i:s');
                    $user->sub_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
                    $user->sub_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
                    $user->is_active_subscription = 1;
                    $user->save();
                    if ($old_subscription_id != '') {
                        try {
                            $old_subscription = \Stripe\Subscription::retrieve($old_subscription_id);
                            // pr($old_subscription);die;
                            if ($old_subscription) {
                                $old_trans = SubscriptionLog::where('subscription_id', $old_subscription_id)->first();
                                // pr($old_trans);die;
                                // $old_subscription->cancel(['refund_policy' => 'refund']);
                                // $old_subscription_res=$old_subscription->delete([
                                //     'prorate' => true
                                // ]);
                                // $old_subscription_res = $old_subscription->delete();
                                // Calculate the amount to refund
                                // $refundAmount = $old_subscription->plan->amount * ($old_subscription->current_period_end - time()) / $old_subscription->plan->interval_count / (60 * 60 * 24);
                                // // Issue a refund for the unused time
                                // $invoice=\Stripe\Invoice::retrieve($old_subscription->latest_invoice);
                                // pr($invoice);die;
                                // $refund = \Stripe\Refund::create([
                                //     'payment_intent' => $invoice->payment_intent,
                                //     'amount' => $refundAmount,
                                //     'reason' => 'cancellation',
                                // ]);
                                // if($old_subscription_res->status== "canceled"){

                                // }
                                // sample prorated invoice for a subscription with quantity of 0
                                // pr($old_subscription->items->data[0]->plan);die;
                                $old_subscription = $old_subscription;
                                $sample_subscription_item = array(
                                    "id"       => $old_subscription->items->data[0]->id,
                                    "plan"     => $old_subscription->items->data[0]->plan->id,
                                    "quantity" => 0,
                                );
                                // pr($sample_subscription_item);die;
                                $upcoming_prorated_invoice = \Stripe\Invoice::upcoming([
                                    "customer"                    => $old_subscription->customer,
                                    "subscription"                => $old_subscription->id,
                                    "subscription_items"          => array($sample_subscription_item),
                                    // "subscription_proration_date" => 'PRORATED_DATE ', // optional
                                ]);
                                $qty = $old_subscription->quantity;
                                // echo $old_subscription->quantity;die;
                                // pr($upcoming_prorated_invoice);die;
                                // find prorated amount
                                $prorated_amount = 0;
                                foreach ($upcoming_prorated_invoice->lines->data as $invoice) {
                                    if ($invoice->type == "invoiceitem") {
                                        $prorated_amount = ($invoice->amount < 0) ? abs($invoice->amount) : 0;
                                        break;
                                    }
                                }
                                if ($qty > 1) {
                                    $commision_amount = ($qty * 0.01) * 100;
                                } else {
                                    $commision_amount = 0;
                                }

                                $final_proration_amount = $prorated_amount - $commision_amount;
                                // pr($final_proration_amount);die;
                                // pr($final_proration_amount);die;
                                // find charge id on the active subscription's last invoice
                                $latest_invoice = \Stripe\Invoice::retrieve($old_subscription->latest_invoice);
                                $latest_charge_id = $latest_invoice->charge;
                                // $pay_id=$latest_invoice->pay();
                                // pr($pay_id);die;

                                // refund amount from last invoice charge
                                if ($final_proration_amount > 0) {
                                    $refund = \Stripe\Refund::create([
                                        'charge' => $latest_charge_id,
                                        'amount' => $final_proration_amount,
                                    ]);
                                    $old_trans->refund_id = $refund->id;
                                    $old_trans->refundable_amount = ($final_proration_amount) / 100;
                                }
                                $old_subscription_res = $old_subscription->delete();

                                if ($old_subscription_res->status == "canceled") {
                                    if ($old_trans) {
                                        $old_trans->status = 3;
                                        $old_trans->save();
                                    }
                                }
                                try {
                                    $subscription = \Stripe\Subscription::retrieve($user->subscription_id);
                                    $subscription->quantity = $qty;
                                    $subscription->prorate = false;
                                    $subscription->save();

                                    try {
                                        Order::where(['subscription_id' => $old_subscription->id, 'is_deducted_commission' => 0])->update(['subscription_id' => $subscription->id]);
                                    } catch (Exception $e) {
                                        Log::channel('subscribe_log')->warning("Can not able to update order USERID: " . $user->id);
                                    }
                                } catch (Exception $e) {
                                    Log::channel('subscribe_log')->critical("Can not update commision qty on subscription " . $user->subscription_id);
                                }
                            }
                            // end temps
                        } catch (\Exception $e) {
                            $error_msg = "Something went wrong!, Old plan was not removed";
                            if ($e->getMessage()) {
                                $error_msg = $e->getMessage();
                            }
                            // pr($error_msg);die;
                            $old_trans = SubscriptionLog::where('subscription_id', $old_subscription_id)->first();
                            if ($old_trans) {
                                $old_trans->status = 2;
                                $old_trans->save();
                            }
                            Log::channel('subscribe_log')->warning('error in cancel subscription->user_id:' . $loginUser->id . 'response->' . $error_msg);
                            // need to store into log
                        }
                    }
                } else {
                    $error_message = trans("translation.Subscription not found, something went wrong!");
                }
            } else {
                $error_message = trans("translation.Subscription not found, something went wrong!");
            }
        }
        if ($error_message) {
            Session::flash('error', $error_message);
        } else {
            Session::flash('success', trans("translation.Subscription has been subscribed successfully"));
        }
        // Handle the success case
        // For example, you could update your database to mark the order as paid
        // You can also redirect the user to a success page or display a success message
        return redirect()->route('dealer.subscription');

        // return view('subscription.success');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('dealer.subscription');
    }

    //make cencel subscription function that i can use anywhere in application
    public function cancelSubscription(Request $request)
    {
        $loginUser = Auth::user();

        if (!$loginUser->subscription_id) {
            Log::channel('subscribe_log')->warning("No active subscription found for user ID: {$loginUser->id}");
            return response()->json(['status' => false, 'message' => 'No active subscription found.']);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Retrieve the subscription from Stripe
            $subscription = Subscription::retrieve($loginUser->subscription_id);

            if ($subscription->status !== 'active') {
                Log::channel('subscribe_log')->warning("Subscription is not active for user ID: {$loginUser->id}");
                return response()->json(['status' => false, 'message' => 'Subscription is not active.']);
            }

            // To cancel at the end of the billing period:
            $canceledSubscription = $subscription->update($subscription->id, ['cancel_at_period_end' => true]);

            // Log the cancellation in SubscriptionLog
            $s_log = new SubscriptionLog;
            $s_log->inv_number = getInvNumber($loginUser);
            $s_log->package_id = $loginUser->package_id; // Assuming the user has a package_id
            $s_log->user_id = $loginUser->id;
            $s_log->subscription_id = $subscription->id;
            $s_log->invoice_id = $subscription->latest_invoice;
            $s_log->subscription_date = date('Y-m-d H:i:s');
            $s_log->sub_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
            $s_log->sub_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
            $s_log->status = 2; // Assuming status '3' represents cancellation
            $s_log->amount = ($subscription->plan->amount) / 100;
            $s_log->response = json_encode($canceledSubscription);
            $s_log->session_id = null; // No session ID for cancellation
            if ($subscription->latest_invoice) {
                $invoice = \Stripe\Invoice::retrieve($subscription->latest_invoice);
                if (!empty($invoice)) {
                    $s_log->transaction_id = $invoice->charge;
                }
            }
            $s_log->created_at = date('Y-m-d H:i:s');
            $s_log->save();

            // Log the cancellation action
            Log::channel('subscribe_log')->info("Subscription canceled for user ID: {$loginUser->id}, Subscription ID: {$subscription->id}");

            return response()->json(['status' => true, 'message' => 'Subscription canceled successfully.']);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            Log::channel('subscribe_log')->critical("Cannot cancel subscription {$loginUser->subscription_id} for user ID: {$loginUser->id}. Error: {$e->getMessage()}");
            return response()->json(['status' => false, 'message' => 'Failed to cancel subscription. Please try again.']);
        } catch (\Exception $e) {
            // Handle general errors
            Log::channel('subscribe_log')->critical("Cannot cancel subscription {$loginUser->subscription_id} for user ID: {$loginUser->id}. Error: {$e->getMessage()}");
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred.']);
        }
    }
}
