<?php

namespace Database\Seeders;

use App\Models\LookUpValue;
use Illuminate\Database\Seeder;

class LookUpValueDetails extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LookUpValue::truncate();
    }
}
