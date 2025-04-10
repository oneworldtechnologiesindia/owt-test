<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('url')->nullable();
            $table->string('image')->nullable();
            $table->enum('size', [1, 2, 3, 4])->default(1)->comment('1=small,2=medium,3=large,4=extra large')->nullable();
            $table->integer('status')->default(1)->comment('1=active,2=deactive')->nullable();
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
        Schema::dropIfExists('ads');
    }
}
