<?php

namespace App\Console\Commands;

use App\User;
use App\Models\Order;
use App\Helpers\MailerFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateOrderInvoiceNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateOrderInvoiceNumber:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order invoice number';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Update order invoice number cron started');

        $orders = Order::all();
        $ordeCount = 0;
        $invoiceNumber = 1000;
        if (isset($orders) && !empty($orders) && count($orders)) {
            foreach ($orders as $order) {
                $ordeCount++;
                $order->invoice_number = 'OC' . ($invoiceNumber + $ordeCount);
                $order->save();
            }
        }

        Log::info('Update order invoice number cron ended');
        return 0;
    }
}
