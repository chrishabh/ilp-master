<?php

namespace App\Console;

use App\Enums\Constants;
use App\Models\ImportExcelTable;
use App\Models\RunningBatchTiming;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\File;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CleanServerDirectory::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $batch_timing = RunningBatchTiming::getBatchTiming(Constants::CLEAN_SERVER_DIR);
        if(!empty($batch_timing)){
            $path = storage_path() . '/logs/batchLogs';
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $filePath = storage_path() . '/logs/batchLogs/CleanServerDirectory.log';
            if (!file_exists($filePath)) {
                File::put($filePath, '');
            }
            $schedule->command('cronJob:clean-server-directory')->cron($batch_timing)->appendOutputTo($filePath);
        }
        $import_time = ImportExcelTable::getCron();
        if(!empty($import_time)){
            $path = storage_path() . '/logs/batchLogs';
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $filePath = storage_path() . '/logs/batchLogs/ImportExcleFile.log';
            if (!file_exists($filePath)) {
                File::put($filePath, '');
            }
            $schedule->command('cronJob:import-excel-files')->cron($import_time)->appendOutputTo($filePath);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
