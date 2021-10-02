<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConstructionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('construction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_description_id');
            $table->foreign('main_description_id')->references('id')->on('main_descritpions');
            $table->unsignedBigInteger('sub_description_id');
            $table->foreign('sub_description_id')->references('id')->on('sub_descritpions');
            $table->text('description')->nullable();
            $table->double('area')->nullable();
            $table->string('unit')->nullable();
            $table->double('lab_rate')->nullable();
            $table->double('total')->nullable();
            $table->double('amount_booked')->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('wages')->nullable();
            $table->double('quantity')->nullable();
            $table->text('booking_description')->nullable();
            $table->string('floor')->nullable();
            $table->unsignedBigInteger('apartment_id');
            $table->foreign('apartment_id')->references('id')->on('apartment_details');
            $table->unsignedBigInteger('block_id');
            $table->foreign('block_id')->references('id')->on('block_details');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('project_details');
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
        Schema::dropIfExists('construction_details');
    }
}
