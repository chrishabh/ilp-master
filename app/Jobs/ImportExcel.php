<?php

namespace App\Jobs;

use App\Models\RunningBatchDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $file = '';
    public $timeout = 3600;  

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_path)
    {
        $this->file = $file_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $batch_id = RunningBatchDetails::batchStarted('Import_Excel_Job');
        importExcelToDB($this->file);
        RunningBatchDetails::batchCompleted($batch_id);
        
    }
}
