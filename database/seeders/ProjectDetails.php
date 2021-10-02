<?php

namespace Database\Seeders;

use App\Models\ProjectDetails as ModelsProjectDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelsProjectDetails::truncate();
        DB::insert("INSERT INTO `project_details`(`project_name`, `deleted_at`, `created_at`, `updated_at`) VALUES 
        ('ILP Site1.0', NULL, current_timestamp(), current_timestamp()),
        ('ILP Site2.0', NULL, current_timestamp(), current_timestamp()),
        ('ILP Site3.0', NULL, current_timestamp(), current_timestamp());");
    }
}
