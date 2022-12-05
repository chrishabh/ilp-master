<?php

namespace App\Models;

use App\Exceptions\AppException;
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
        $noOfRecord =  130;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $query = ApartmentDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])
        ->where('block_id',$request['block_id']);
        if(is_array($request['floor_id'])){
            $data = $query->whereIn('floor_id',$request['floor_id'])->offset($offset)->limit($noOfRecord)->get();
        }else{
            $data = $query->where('floor_id',$request['floor_id'])->offset($offset)->limit($noOfRecord)->get();
        }
        

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getApartmentTotalRecords($request)
    {
        return ApartmentDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])
        ->where('block_id',$request['block_id'])->where('floor_id',$request['floor_id'])->count('id');
    }

    public static function addApartmentDetails($data){

        return ApartmentDetails::insertGetId($data);
    }

    public static function addApartmentDetailsAndFetch($data){

        $return = ApartmentDetails::whereNull('deleted_at')->where('apartment_number',$data['apartment_number'])->where('floor_id',$data['floor_id'])->where('block_id',$data['block_id'])->where('project_id',$data['project_id'])->first();

        if(isset($return->id)){
            return $return->id;
           
        } else {
            return ApartmentDetails::insertGetId($data);
        }
    }


    public static function getApartmentId($apartment_name)
    {
        $return = ApartmentDetails::whereNull('deleted_at')->where('apartment_number',$apartment_name)->first();

        if(isset($return->id)){
            return $return->id;
           
        } else {
            return NULL;
        }
    }

    public static function  deleteApartmentByProject($project_id)
    {
        return ApartmentDetails::where('project_id',$project_id)->update(['deleted_at' =>date('Y-m-d')]);
    }
}
