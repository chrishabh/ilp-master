<?php

namespace Database\Seeders;

use App\Models\LookUpValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        DB::insert("INSERT INTO `look_up_values` ( `module`, `type`, `key`, `text`, `sort_order`, `deleted_at`, `created_at`, `updated_at`) VALUES 
        ('wages', 'trade', 'Fire Stop', 'Fire Stop', '0', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ,('wages', 'trade', 'Fixer', 'Fixer', '1', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ,('wages', 'trade', 'Labour', 'Labour', '2', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ,('wages', 'trade', 'Making Good', 'Making Good', '3', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ,('wages', 'trade', 'Plaster/Render', 'Plaster/Render', '4', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ,('wages', 'trade', 'Screed', 'Screed', '5', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ,('wages', 'trade', 'SFS', 'SFS', '6', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);");
    }
}
