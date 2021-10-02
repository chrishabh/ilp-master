<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWagesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wages_details', function (Blueprint $table) {
            $table->id();
            $table->string('pay_to')->nullable();
            $table->string('trade')->nullable();
            $table->unsignedBigInteger('level')->nullable();
            $table->unsignedBigInteger('block_id')->nullable();
            $table->double('plot_or_room')->nullable();
            $table->string('description_work')->nullable();
            $table->string('m2_or_hours')->nullable();
            $table->double('rate')->nullable();
            $table->double('sum')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wages_details');
    }
}
