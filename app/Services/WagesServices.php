<?php

namespace App\Services;

use App\Models\ConstructionDetails;
use App\Models\WagesDetails;

class WagesServices{

    public static function bookWages($request)
    {
       WagesDetails::bookWages($request);
    }

    public static function getWages($request)
    {
        return  WagesDetails::getWages($request);
    }

    public static function getWagesExcel($request)
    {
        $data =  WagesDetails::getWages($request);
        $records = $excel_data = [];
        foreach($data['wages_details'] as $value){

            $records['Pay To:'] = $value['pay_to'];
            $records['Trade'] = $value['trade'];
            $records['Level'] = $value['level'];
            $records['Block'] = $value['block_id'];
            $records['Plot/room'] = $value['plot_or_room'];
            $records['Description of work'] = $value['description_work'];
            $records['m2 (or hours)'] = $value['m2_or_hours'];
            $records['Rate'] = $value['rate'];
            $records['Sum'] = $value['sum'];

            $excel_data [] = $records;
        }

        getXlsxFile($excel_data, 'Wages Booking');
    }

}
