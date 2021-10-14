<?php

namespace App\Services;

use App\Models\ConstructionDetails;
use App\Models\WagesDetails;

class WagesServices{

    public static function bookWages($request)
    {
        $data = $request->toArray();
        foreach($data['book_wages'] as &$value){
            $value['floor'] = $value['level'];
            unset($value['level']);
           WagesDetails::bookWages($value);
           ConstructionDetails::addWagesBookValue($value);
        }
        
    }

    public static function getWages($request)
    {
        $return = WagesDetails::getWages($request);

        $records = $excel_data = [];
        foreach($return['wages_details'] as $value){

            $records['Pay To:'] = $value['pay_to'];
            $records['Trade'] = $value['trade'];
            $records['Level'] = $value['level'];
            $records['Block'] = $value['block_id'];
            $records['Plot/room'] = $value['plot_or_room'];
            $records['Description of work'] = $value['description_work'];
            $records['m2 (or hours)'] = $value['m2_or_hours'];
            $records['Rate'] = $value['rate'];
            $records['Booking Amount'] = $value['sum'];

            $excel_data [] = $records;
        }

        $return['excel_url'] = getXlsxFile($excel_data, 'Wages_Booking');

        return $return;
    }

    public static function getWagesExcel($request)
    {
        $return = WagesDetails::getWages($request);
       
        $i = 0;
        $records = $excel_data = [];
        foreach($return['wages_details'] as $value){

            $records['Pay To:'] = $value['pay_to'];
            $records['Trade'] = $value['trade'];
            $records['Level'] = $value['level'];
            $records['Block'] = $value['block_id'];
            $records['Plot/room'] = $value['plot_or_room'];
            $records['Description of work'] = $value['description_work'];
            $records['m2 (or hours)'] = $value['m2_or_hours'];
            $records['Rate'] = $value['rate'];
            $records['Sum'] = $value['sum'];

            if($i%2 == 0){
                $excel_data ['I.L.P.1.0'][] = $records;
            }else{
                $excel_data ['I.L.P.2.0'][] = $records;
            }
            
           
            $i++;
        }
        $return['excel_url'] = getXlsxFiles($excel_data, 'Wages_Booking');

        return $return;
    }

}
