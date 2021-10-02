<?php

namespace Database\Seeders;

use App\Models\BlockDetails as ModelsBlockDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ModelsBlockDetails::truncate();
        DB::insert("INSERT INTO `block_details` (`block_name`,`project_id`, `deleted_at`, `created_at`, `updated_at`) VALUES 
        ( 'I','1', NULL, current_timestamp(), current_timestamp()),
        ( 'L','2', NULL, current_timestamp(), current_timestamp());");
    }
}
