<?php

namespace Database\Seeders;

use App\Models\MainDescritpion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainDescriptionDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MainDescritpion::truncate();
        DB::insert("INSERT INTO `main_descritpions`(`description`, `deleted_at`, `created_at`, `updated_at`) VALUES
        ('Metal 1st Fix - Dwelings', NULL, current_timestamp(), current_timestamp()),
        ('Metal 2nd Fix - Dwelings', NULL, current_timestamp(), current_timestamp()),
        ('Insulation to Dwelings', NULL, current_timestamp(), current_timestamp()),
        ('Miscellaneous items to Dwelings', NULL, current_timestamp(), current_timestamp()),
        ('Wall Linings to Dwelings', NULL, current_timestamp(), current_timestamp());");
    }
}
