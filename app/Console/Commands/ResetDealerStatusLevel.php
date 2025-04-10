<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetDealerStatusLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ResetDealerStatusLevel:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::info('Dealer status level reset cron started');

        User::query()
            ->where('role_type', '2')
            ->whereNull('deleted_at')
            ->update(['status_level' => '0', 'turnover' => '0.00']);

        Log::info('Dealer status level reset cron ended');
        return 0;
    }
}
