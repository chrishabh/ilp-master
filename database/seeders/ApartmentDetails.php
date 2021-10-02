<?php

namespace Database\Seeders;

use App\Models\ApartmentDetails as ModelsApartmentDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApartmentDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('apartment_details')->truncate();
        DB::insert("INSERT INTO `apartment_details`(`apartment_number`, `block_id`, `project_id`, `deleted_at`, `created_at`, `updated_at`) VALUES 
        ( 'I.02.01','1','1', NULL, current_timestamp(), current_timestamp()),
        ( 'I.02.02','1','1', NULL, current_timestamp(), current_timestamp()),
        ( 'I.02.03','1','2', NULL, current_timestamp(), current_timestamp()),
        ( 'L.02.01','2','2', NULL, current_timestamp(), current_timestamp()),
        ( 'L.02.02','2','1', NULL, current_timestamp(), current_timestamp()),
        ( 'L.02.03','2','3', NULL, current_timestamp(), current_timestamp()),
        ( 'L.02.04','2','1', NULL, current_timestamp(), current_timestamp());");
    }
}
