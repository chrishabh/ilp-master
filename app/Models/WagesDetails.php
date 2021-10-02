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
    }

    public static function getWages($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $return['total_records'] = WagesDetails::whereNull('deleted_at')->count('id');

        $data = WagesDetails::whereNull('deleted_at')->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }
        return $return;
    }
}
