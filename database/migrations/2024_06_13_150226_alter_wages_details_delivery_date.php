<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWagesDetailsDeliveryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wages_details', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('sub_description_id');
            $table->unsignedBigInteger('created_by')->nullable()->after('delivery_date');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wages_details', function (Blueprint $table) {
            //
        });
    }
}
