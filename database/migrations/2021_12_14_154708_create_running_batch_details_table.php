<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRunningBatchDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('running_batch_details', function (Blueprint $table) {
            $table->id();
            $table->string('batch_type',50)->nullable();
            $table->dateTime('started_at')->useCurrent();;
            $table->string('progress',50)->nullable();
            $table->string('progress_comment',50)->nullable();
            $table->dateTime('completed_at')->nullable();
            //Default Mandatory Metadata fields
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
        Schema::dropIfExists('running_batch_details');
    }
}
