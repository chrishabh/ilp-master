<?php

namespace Database\Seeders;

use App\Models\RunningBatchDetails;
use App\Models\RunningBatchTiming as ModelsRunningBatchTiming;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RunningBatchTiming extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelsRunningBatchTiming::truncate();
        DB::insert("INSERT INTO `running_batch_timings` (`id`, `batch_name`, `cron_timing`, `deleted_at`, `created_at`, `updated_at`) VALUES 
        (NULL, 'clean_server_directory', '00 00 * * *', NULL, current_timestamp(), current_timestamp());");
    }
}
