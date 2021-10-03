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

        $data = WagesDetails::whereNull('deleted_at')
        ->where('project_id',$request['project_id'])
        ->where('block_id',$request['block_id'])
        ->where('apartment_id',$request['apartment_id'])
        ->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }else{
            $return['wages_details'] = [];
        }
        return $return;
    }
}
