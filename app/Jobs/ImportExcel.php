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
    public $file = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_path)
    {
        $this->handle($file_path);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($file_path)
    {
        $batch_id = RunningBatchDetails::batchStarted('Import_Excel_Job');
        importExcelToDB($file_path);
        RunningBatchDetails::batchCompleted($batch_id);
        
    }
}
