<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function getApartmentDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = ApartmentDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])
        ->where('block_id',$request['block_id'])->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getApartmentTotalRecords($request)
    {
        return ApartmentDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])
        ->where('block_id',$request['block_id'])->count('id');
    }

    public static function addApartmentDetails($data){

        return ApartmentDetails::insertGetId($data);
    }
}
