<?php

namespace App\Console\Commands;

use App\Models\RunningBatchDetails;
use App\Services\UserServices;
use Illuminate\Console\Command;

class CleanServerDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronJob:clean-server-directory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Server Directory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "Start time " .date('Y-m-d h:i:s') . "\n"; 
        echo "Clean Server Directory \n";
        $batch_id = RunningBatchDetails::batchStarted('Clean Server Directory');
        UserServices::cleanServerDirectory();
        RunningBatchDetails::batchCompleted($batch_id);
        echo "End time " .date('Y-m-d h:i:s'). "\n";
        echo "**********************************************************************************\n"; 
    }
}
