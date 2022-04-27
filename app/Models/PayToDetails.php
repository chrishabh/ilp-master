<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayToDetails extends Model
{
    use HasFactory;

    public static function getPayToDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = PayToDetails::whereNull('deleted_at')->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getPayToTotalRecords()
    {
        return PayToDetails::whereNull('deleted_at')->count('id');
    }

    public static function addPayToDetails($data)
    {
        return PayToDetails::insertGetId($data);
    }

    public static function getPayToCode($pay_name)
    {
        return PayToDetails::whereNull('deleted_at')->where('pay_to_name',$pay_name)->first();
    }
}
