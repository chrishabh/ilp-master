<?php

namespace App\Console\Commands;

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
    protected $description = 'Command description';

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
        UserServices::cleanServerDirectory();
        echo "End time " .date('Y-m-d h:i:s'). "\n";
        echo "**********************************************************************************\n"; 
    }
}
