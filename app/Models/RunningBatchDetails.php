<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningBatchDetails extends Model
{
    use HasFactory;

    public static function batchStarted($batch_type){
        $data = [
            'batch_type'=>$batch_type,
            'progress' => '0%',
            'progress_comment' => 'in_progress'
        ];

        return RunningBatchDetails::insertGetId($data);
    }

    public static function batchCompleted($id)
    {
        $data = [
            'progress' => '100%',
            'progress_comment' => 'completed',
            'completed_at' => date('Y-m-d h:i:s')
        ];

        RunningBatchDetails::where('id',$id)->update($data);
    }
}
