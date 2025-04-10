<?php

namespace App\Console\Commands;

use \App\User;
use Stripe\Stripe;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAccountStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckAccountStatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check user account status is active or deactive or connect or disconnect.';

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
        Log::channel('account_cron')->warning("Start Check Account status cron started");

        Stripe::setApiKey(config('services.stripe.secret'));

        $date = Carbon::now();

        $users = User::whereRaw('stripe_account_id!=""')->whereNotNull('stripe_account_id')->get();
        if($users){
            foreach ($users as $key => $user) {
                $account_id = $user->stripe_account_id;
                $account = \Stripe\Account::retrieve($account_id);
                if ($account) {
                    $status = 4; // created
                    if (isset($account->verification->disabled_reason) && $account->verification->disabled_reason == '') {            // $status = 5; // disabled
                        $status = 2;
                    } elseif ((isset($account->charges_enabled) && $account->charges_enabled) || (isset($account->payouts_enabled) && $account->payouts_enabled)) {
                        $status = 1;
                    } elseif ($account->details_submitted == 1) {
                        $status = 2;
                    }
                    Log::channel('account_cron')->warning("stripe_account_status USER:".$user->id.", Status:".$status);
                    $user->stripe_account_status = $status;
                    $user->save();
                }
            }
        }
        Log::channel('account_cron')->warning("END Check account status cron end");

        return 0;
    }
}
