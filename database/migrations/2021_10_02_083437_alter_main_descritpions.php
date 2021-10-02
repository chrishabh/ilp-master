<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMainDescritpions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_descritpions', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->after('description');
            $table->unsignedBigInteger('block_id')->after('description');
            $table->unsignedBigInteger('apartment_id')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_descritpions', function (Blueprint $table) {
            //
        });
    }
}
