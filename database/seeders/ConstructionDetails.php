<?php

namespace Database\Seeders;

use App\Models\ConstructionDetails as ModelsConstructionDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstructionDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ModelsConstructionDetails::truncate();
        DB::insert("INSERT INTO `construction_details`(`main_description_id`, `sub_description_id`, `description`, `area`, `unit`, `lab_rate`, `total`, `amount_booked`, `name`, `wages`, `quantity`, `booking_description`, `floor`, `apartment_id`,`block_id`,`project_id`,`deleted_at`, `created_at`, `updated_at`) VALUES ('1','1','I.02.01','1','1','1','1','1','1','1','1','1','1','1','1','1', NULL, current_timestamp(), current_timestamp()),
        ('1','1','ILP 34 (metal)','0','m2','3.50','0.00','0','ilp','0','0','asdfg','F2','1','1','1', NULL, current_timestamp(), current_timestamp()),
        ('1','2','ILP 29 (metal)','0','m2','3.20','0.00','0','ilp','0','0','asdfg','F2','1','1','1', NULL, current_timestamp(), current_timestamp()),
        ('1','2','ILP 30 (metal)','29','m2','3.20','93.13','147','GGBM Drylining','184','46.00','ILP - 30/32 - Metal','F2','1','1','1', NULL, current_timestamp(), current_timestamp()),
        ('1','2','ILP 32 (metal)','17','m2','3.20','55.88','0','ilp','0','0','asdfg','F2','1','1','1', NULL, current_timestamp(), current_timestamp()),
        ('1','2','ILP 33 (metal)','0','m2','3.20','0.00','0','ilp','0','0','asdfg','F2','1','1','1', NULL, current_timestamp(), current_timestamp()),
        ('1','3','ILP 31 (metal)','0','m2','3.20','0.00','0','ilp','0','0','asdfg','F2','1','1','1', NULL, current_timestamp(), current_timestamp());");
    }
}
