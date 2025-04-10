<?php

namespace App\Console\Commands;

use App\User;
use App\Models\Order;
use App\Helpers\MailerFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUserDisplayId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateUserDisplayId:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user display id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Update user display id cron started');

        $users = User::all();
        $customerid = $dealerid = 1000;
        $customercount = $dealercount = 0;
        if (isset($users) && !empty($users) && count($users)) {
            foreach ($users as $user) {
                if ($user->role_type == 2) {
                    $dealercount++;
                    $user->display_id = 'HQD' . ($dealerid + $dealercount);
                }
                if ($user->role_type == 3) {
                    $customercount++;
                    $user->display_id = 'HQC' . ($customerid + $customercount);
                }
                $user->save();
            }
        }

        Log::info('Update user display id cron ended');
        return 0;
    }
}
