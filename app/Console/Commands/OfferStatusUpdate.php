<?php

namespace App\Console\Commands;

use App\Models\OfferPurchaseEnquiry;
use App\Models\PurchaseEnquiry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class OfferStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offerStatusUpdate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Offer status check and update accordingly.';

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
        Log::info('Offer status update cron started');

        $date = Carbon::now();

        PurchaseEnquiry::query()
            ->where('created_at', '<', $date->subHours(24))
            ->update(['status' => 4]);

        OfferPurchaseEnquiry::query()
            ->where('created_at', '<', $date->subHours(12))
            ->update(['status' => 4]);

        Log::info('Offer status update cron ended');
        return 0;
    }
}
