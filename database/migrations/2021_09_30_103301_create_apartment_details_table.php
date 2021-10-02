<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateApartmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_details', function (Blueprint $table) {
            $table->id();
            $table->string('apartment_number');
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
        Schema::dropIfExists('apartment_details');
    }
}
