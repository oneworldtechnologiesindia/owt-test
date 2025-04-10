<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_types', function (Blueprint $table) {
            $table->id();
            $table->string('plan_type');
            $table->decimal('silver_level', 5, 2)->default(0);
            $table->decimal('gold_level', 5, 2)->default(0);
            $table->decimal('platinum_level', 5, 2)->default(0);
            $table->decimal('diamond_level', 5, 2)->default(0);
            $table->enum('type', ['DEALER', 'DISTRIBUTER'])->default('DEALER');
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
        Schema::dropIfExists('plan_types');
    }
}
