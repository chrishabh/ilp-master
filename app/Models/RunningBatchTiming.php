<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningBatchTiming extends Model
{
    use HasFactory;

    public static function getBatchTiming($batch_type)
    {
        $return = RunningBatchTiming::select('cron_timing')->whereNull('deleted_at')->where('batch_name',$batch_type)->first();
        if(!empty($return['cron_timing'])){
            return $return['cron_timing'];
        }

        return null;
    }
}
