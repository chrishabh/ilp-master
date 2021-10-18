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
        WagesDetails::insert($request);
    }

    public static function getWages($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $return['total_records'] = WagesDetails::whereNull('deleted_at')->count('id');

        $data = WagesDetails::join('project_details','wages_details.project_id','=','project_details.id')
        ->join('block_details','wages_details.block_id','=','block_details.id')
        //->join('apartment_details','wages_details.apartment_id','=','apartment_details.id')
        ->select('wages_details.pay_to','wages_details.trade','wages_details.floor as level','wages_details.block_id','wages_details.plot_or_room','wages_details.description_work','wages_details.m2_or_hours','wages_details.rate','wages_details.sum','wages_details.apartment_id','wages_details.main_description_id','wages_details.project_id','project_details.project_name','block_details.block_name','wages_details.apartment_id as apartment_number')
        ->whereNull('wages_details.deleted_at')
        ->where('wages_details.project_id',$request['project_id'])
        ->where('wages_details.block_id',$request['block_id'])
        //->where('wages_details.apartment_id',$request['apartment_id'])
        ->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }else{
            $return['wages_details'] = [];
        }
        return $return;
    }
}
