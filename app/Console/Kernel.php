<?php

namespace App\Console;

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
        $path = storage_path() . '/logs/batchLogs';
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $filePath = storage_path() . '/logs/batchLogs/CleanServerDirectory.log';
        if (!file_exists($filePath)) {
            File::put($filePath, '');
        }
        $schedule->command('cronJob:clean-server-directory')->cron('00 00 * * *')->appendOutputTo($filePath);
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
