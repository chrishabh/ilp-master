<?php

namespace Database\Seeders;

use App\Models\SubDescritpion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubDescriptionDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SubDescritpion::truncate();
        DB::insert("INSERT INTO `sub_descritpions`(`sub_description`, `main_description_id`, `deleted_at`, `created_at`, `updated_at`) VALUES 
        ('Metal - 90mm `I` studs','1', NULL, current_timestamp(), current_timestamp()), 
        ('Metal - 70mm `C` studs','1', NULL, current_timestamp(), current_timestamp()),
        ('Metal - 50mm `C` studs','1', NULL, current_timestamp(), current_timestamp()), 
        ('Plasterboard to one side','1', NULL, current_timestamp(), current_timestamp()), 
        ('Boarding','2', NULL, current_timestamp(), current_timestamp()), 
        ('25mm Isover Acoustic Partition Roll (APR 1200)','3', NULL, current_timestamp(), current_timestamp()), 
        ('60mm `I` studs','5', NULL, current_timestamp(), current_timestamp()), 
        ('Gypliner','5', NULL, current_timestamp(), current_timestamp()), 
        ('Insulation','5', NULL, current_timestamp(), current_timestamp()), 
        ('Boardings','5', NULL, current_timestamp(), current_timestamp()); ");
    }
}
