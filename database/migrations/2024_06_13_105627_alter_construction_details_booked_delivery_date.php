<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterConstructionDetailsBookedDeliveryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('construction_details', function (Blueprint $table) {
            $table->string('booking_date')->nullable()->after('project_id');
            $table->string('delivery_date')->nullable()->after('booking_date');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('construction_details', function (Blueprint $table) {
            //
        });
    }
}
