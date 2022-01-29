<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddedFloorIdWagesDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wages_details', function (Blueprint $table) {
            $table->unsignedBigInteger('floor_id')->nullable()->after('apartment_id');
            $table->unsignedBigInteger('apartment_id')->nullable()->change();
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
