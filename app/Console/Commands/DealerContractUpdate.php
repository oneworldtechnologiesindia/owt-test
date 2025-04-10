<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DealerContractUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DealerContractUpdate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update dealer contract';

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
        Log::info('Dealer contract update cron started');
        $users = User::where('role_type', 2)->whereNull('contract_canceldate')->whereNull('deleted_at')->get();
        foreach ($users as $user) {
            $date = Carbon::now();
            if (!isset($user->contract_startdate)) {
                $uDate = Carbon::parse($user->created_at);
                $new_contract_enddate  = $uDate->copy()->addMonths(13);
                $user->contract_startdate = $uDate->copy()->format('Y-m-d');
                $user->contract_enddate = $new_contract_enddate->format('Y-m-d');
            }
            $contractEnddate = Carbon::parse($user->contract_enddate);
            if ($contractEnddate < $date && !isset($user->contract_canceldate)) {
                $uDate = Carbon::parse($user->contract_enddate);
                $user->contract_enddate = $uDate->copy()->addMonths(12)->format('Y-m-d');
            }
            $user->save();
        }
        Log::info('Dealer contract update cron ended');
        return 0;
    }
}
