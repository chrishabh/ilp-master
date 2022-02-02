<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function addFloor($data){

        $return = Floor::whereNull('deleted_at')->where('block_id',$data['block_id'])->where('project_id',$data['project_id'])->where('floor_name',$data['floor_name'])->first();
        if(isset($return->id)){
            return $return->id;
           
        } else {
            return Floor::insertGetId($data);
        }
        //return Floor::insertGetId($data);
    }

    public static function getFloorDetails($request){

        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = Floor::whereNull('deleted_at')->where('project_id',$request['project_id'])
        ->where('block_id',$request['block_id'])->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function  deleteFloorByProject($project_id)
    {
        return Floor::where('project_id',$project_id)->update(['deleted_at' =>date('Y-m-d')]);
    }
}
