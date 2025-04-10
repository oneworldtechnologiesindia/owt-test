<?php

namespace App\Console\Commands;

use \App\User;
use Stripe\Stripe;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckSubscriptionStatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check user subscription is active or deactive.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('subscription_cron')->warning("Check subscription status cron started");

        Stripe::setApiKey(config('services.stripe.secret'));

        $date = Carbon::now();

        $users = User::whereRaw('DATE(sub_period_end) <= DATE(NOW()) AND subscription_id!="" AND is_active_subscription=1')->whereNotNull('subscription_id')->get();
        if($users){
            foreach ($users as $key => $user) {
                $subscription = \Stripe\Subscription::retrieve($user->subscription_id);
                if (!empty($subscription) && $subscription->status == "canceled") {
                    $user->subscription_id = NULL;
                    $user->sub_period_start = NULL;
                    $user->sub_period_end = NULL;
                    $user->subscription_date = NULL;
                    $user->is_active_subscription = 0;
                    // $user->is_subscription_canceled = 0;
                    $user->save();
                    Log::channel('subscription_cron')->warning(" getting subscription status was canceled");
                }
            }
        }
        Log::channel('subscription_cron')->warning("Check subscription status cron end");

        return 0;
    }
}
