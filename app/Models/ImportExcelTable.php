<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportExcelTable extends Model
{
    use HasFactory;

    
    public static function insertFilePath($data){

        return ImportExcelTable::insertGetId($data);
    }

    public static function deleted_at($file_path){

        return ImportExcelTable::where('file_path',$file_path)->update(['deleted_at'=> date("Y-m-d")]);
    }

    public static function getCron(){

        $return = ImportExcelTable::select('cron_timing')->whereNull('deleted_at')->orderBy('id','DESC')->first();
        if(!empty($return['cron_timing'])){
            return $return['cron_timing'];
        }
    }

    public static function getFile(){

        $return = ImportExcelTable::select('file_path')->whereNull('deleted_at')->orderBy('id','DESC')->first();
        if(!empty($return['file_path'])){
            return $return['file_path'];
        }
    }

    public static function progressUpdate($file_path,$progress){

        return ImportExcelTable::where('file_path',$file_path)->update(['progress'=> $progress]);
    }

    public static function completeUpdate($file_path){

        return ImportExcelTable::where('file_path',$file_path)->update(['completed_at'=> date("Y-m-d")]);
    }

    public static function getProgress(){

        $return = ImportExcelTable::select('progress')->whereNull('deleted_at')->orderBy('id','DESC')->first();
        if(!empty($return['progress'])){
            return $return['progress'];
        }
        return 0;
    }
}
