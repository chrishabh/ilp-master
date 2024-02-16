<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLevelDescriptionAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_project_linkings', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_description_id')->nullable()->after('project_id');
            $table->unsignedBigInteger('main_description_id')->nullable()->after('project_id');
            $table->unsignedBigInteger('floor_id')->nullable()->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_project_linkings', function (Blueprint $table) {
            //
        });
    }
}
