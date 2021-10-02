<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubDescritpions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_descritpions', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->after('main_description_id');
            $table->unsignedBigInteger('block_id')->after('main_description_id');
            $table->unsignedBigInteger('apartment_id')->after('main_description_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_descritpions', function (Blueprint $table) {
            //
        });
    }
}
