<?php

namespace App\Console\Commands;

use App\User;
use App\Models\Order;
use App\Helpers\MailerFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateDealerStatusLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateDealerStatusLevel:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $mailer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MailerFactory $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Dealer staus level update cron started');

        $orders = Order::query()
            ->select(DB::raw("SUM(orders.amount) as turnover"), 'orders.dealer_id')
            ->join('users as dealer', 'dealer.id', '=', 'orders.dealer_id')
            ->whereYear('orders.created_at', date('Y'))
            ->whereNull('orders.deleted_at')
            ->whereNull('dealer.deleted_at')
            ->where('dealer.role_type', '2')
            ->where('dealer.status', '1')
            ->where('orders.status', '!=', '3')
            ->groupBy('orders.dealer_id')
            ->get()
            ->pluck('turnover', 'dealer_id')
            ->toArray();

        foreach ($orders as $dealer => $turnover) {
            $turnover = (float) round($turnover, 2);
            $user = User::find($dealer);
            $user->turnover = $turnover;

            switch ($turnover) {
                case ($turnover > 150000):
                    $this->mailer->sendDealerStatusLevelUpdateEmail($user, 3);
                    $user->status_level = '3';
                    break;

                case ($turnover > 100000):
                    $this->mailer->sendDealerStatusLevelUpdateEmail($user, 2);
                    $user->status_level = '2';
                    break;

                case ($turnover > 75000):
                    $this->mailer->sendDealerStatusLevelUpdateEmail($user, 1);
                    $user->status_level = '1';
                    break;

                default:
                    $this->mailer->sendDealerStatusLevelUpdateEmail($user, 0);
                    $user->status_level = '0';
            }

            if ($user->save()) {
                Log::info('Dealer staus level update cron ended');
            } else {
                Log::info('Dealer staus level not update.');
            }
        }
        Log::info('Dealer staus level update cron ended');
        return 0;
    }
}
