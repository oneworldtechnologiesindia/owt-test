<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxToSubscriptionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_log', function (Blueprint $table) {
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('amount_total', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_log', function (Blueprint $table) {
            $table->dropColumn('tax');
            $table->dropColumn('amount_total');
        });
    }
}
