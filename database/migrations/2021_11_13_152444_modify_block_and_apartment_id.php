<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyBlockAndApartmentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('construction_details', function (Blueprint $table) {
            $table->unsignedBigInteger('apartment_id')->nullable()->change();
            $table->unsignedBigInteger('block_id')->nullable()->change();
            DB::statement('alter table construction_details modify area DOUBLE(15,2) DEFAULT 0');
            DB::statement('alter table construction_details modify total DOUBLE(15,2) DEFAULT 0');
            DB::statement('alter table construction_details modify lab_rate DOUBLE(15,2) DEFAULT 0');
            $table->string('amount_booked')->nullable()->change();
            $table->string('wages')->nullable()->change();
            $table->string('quantity')->nullable()->change();
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
