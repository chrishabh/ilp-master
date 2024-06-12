<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWagesDetailsApartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wages_details', function (Blueprint $table) {
            $table->unsignedBigInteger('apartment_id')->after('sum');
            $table->unsignedBigInteger('project_id')->after('apartment_id');

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
