<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function getBlockDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = BlockDetails::whereNull('deleted_at')->whereNull('project_id')->ORwhere('project_id',$request['project_id'])->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getBlockTotalRecords($request)
    {
        return BlockDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->count('id');
    }

    public static function addBlockDetails($data){

        return BlockDetails::insertGetId($data);
    }

    public static function getBlockId($block_name,$project_id)
    {
        $return = BlockDetails::whereNull('deleted_at')->where('block_name',$block_name)->first();

        if(isset($return->id)){
            return $return->id;
           
        } else {
            $data = [
                "block_name" => (!empty($block_name))?$block_name:'N/A',
                "project_id" => $project_id
            ];
            return BlockDetails::insertGetId($data);
        }
    }

    public static function getBlockName($block_id)
    {
        return BlockDetails::whereNull('deleted_at')->where('id',$block_id)->first();
    }

    public static function deleteBlockByProject($project_id)
    {
        return BlockDetails::where('project_id',$project_id)->update(['deleted_at' =>date('Y-m-d')]);
    }

    public static function insertProjectForExcelImport($data)
    {
        return BlockDetails::insertGetId($data);
    }

    public static function getBlockIdForPart($project,$part){

        $return = BlockDetails::whereNull('deleted_at')->where('project_id',$project)->get();
        $firt_part = $second_part = [];
        if(count($return)>0){
            $data = $return->toArray();
            if($part == '1'){
                for($i=0; $i<count($data)/2; $i++){
                    $firt_part [] = $data[$i];
                }
                return $firt_part;
            }elseif($part == '2'){
                for($i=count($data)/2; $i<count($data); $i++){
                    $second_part [] = $data[$i];
                }
                return $second_part;
            }
        }else{
            return [];
        }
    }
}
