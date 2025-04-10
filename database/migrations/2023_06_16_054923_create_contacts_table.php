<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->integer('dealer_id')->nullable();
            $table->integer('contact_type')->default(1)->comment('1=Manufacturer,2=Distributor,3=Dealer')->nullable();
            $table->integer('status')->default(1)->comment('1=active,2=deactive')->nullable();
            $table->string('company')->nullable();
            $table->integer('salutation')->default(1)->comment('1=Herr,2=Frau,3=Divers')->nullable();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('email')->nullable();
            $table->string('street')->nullable();
            $table->string('street_nr')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('telephone')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
