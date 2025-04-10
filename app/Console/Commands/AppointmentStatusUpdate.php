<?php

namespace App\Console\Commands;

use App\Models\AppointmentDealer;
use App\Models\OfferPurchaseEnquiry;
use App\Models\PurchaseEnquiry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AppointmentStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AppointmentStatusUpdate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Appointment status check and update accordingly.';

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
        Log::info('Appointment status update cron started');

        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i:s');
        $time_add = Carbon::now()->subHours(2)->format('H:i:s');

        AppointmentDealer::query()
            ->where('appo_date', '<', $date)
            ->where('appo_time', '<', $time)
            ->where('status', 1)
            ->update(['status' => 8]);

        AppointmentDealer::query()
            ->where('appo_date', '<', $date)
            ->where('appo_time', '<', $time_add)
            ->where('status', 2)
            ->update(['status' => 8]);

        AppointmentDealer::query()
            ->where('reschedule_appo_date', '<', $date)
            ->where('reschedule_appo_time', '<', $time)
            ->where('status', 6)
            ->update(['status' => 8]);

        AppointmentDealer::query()
            ->where('reschedule_appo_date', '<', $date)
            ->where('reschedule_appo_time', '<', $time_add)
            ->where('status', 7)
            ->update(['status' => 8]);

        Log::info('Appointment status update cron ended');
        return 0;
    }
}
