<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WagesDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function bookWages($request)
    {
        WagesDetails::insert($request->toArray());
        ConstructionDetails::addWagesBookValue($request);
    }

    public static function getWages($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $return['total_records'] = WagesDetails::whereNull('deleted_at')->count('id');

        $data = WagesDetails::join('project_details','wages_details.project_id','=','project_details.id')
        ->join('block_details','wages_details.block_id','=','block_details.id')
        ->join('apartment_details','wages_details.apartment_id','=','apartment_details.id')
        ->select('wages_details.*','project_details.project_name','block_details.block_name','apartment_details.apartment_number')
        ->whereNull('wages_details.deleted_at')
        ->where('wages_details.project_id',$request['project_id'])
        ->where('wages_details.block_id',$request['block_id'])
        ->where('wages_details.apartment_id',$request['apartment_id'])
        ->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }else{
            $return['wages_details'] = [];
        }
        return $return;
    }
}
