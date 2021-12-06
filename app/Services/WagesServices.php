<?php

namespace App\Services;

use App\Models\BlockDetails;
use App\Models\ConstructionDetails;
use App\Models\PayToDetails;
use App\Models\User;
use App\Models\WagesDetails;

class WagesServices{

    public static function bookWages($request)
    {
        $data = $request->toArray();
        foreach($data['book_wages'] as &$value){
            $value['floor'] = $value['level'];
            //$value['user_id'] = User::details()->id;
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

            $records['Pay To:'] = $value['pay_to'];     // Coloumn A
            $records['Trade'] = $value['trade'];    // Coloumn B
            $records['Level'] = $value['level'];    // Coloumn C
            $records['Block'] = BlockDetails::getBlockName($value['block_id'])->block_name;     // Coloumn D
            $records['Plot/room'] = $value['plot_or_room'];     // Coloumn E
            $records['Description of work'] = $value['description_work'];       // Coloumn F
            $records['m2 (or hours)'] = $value['m2_or_hours'];      // Coloumn G
            $records['Rate'] = $value['rate'];      // Coloumn H
            $records['Booking Amount'] = "£".roundOff($value['amount']);     // Coloumn I
            $records['Instruction required (y/n)'] = '';        // Coloumn J
            $records['Instruction received (y/n)'] = '';        // Coloumn K
            $records[' '] = '';         // Coloumn L
            $records['Approved'] = "£".roundOff($value['amount']);       // Coloumn M
            $records['Difference'] = '';        // Coloumn N
            $records['Surveyor comments'] = '';     // Coloumn O
            $records['measured'] = roundOff($value['amount']);       // Coloumn P
            $records['Possible VO'] = '';       // Coloumn Q
            $records['variation'] = '';     // Coloumn R
            $records['non recov'] ='';      // Coloumn S
            $records['CHECK'] = '';     // Coloumn T
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

    public static function addPayToDetails($request)
    {
       return PayToDetails::addPayToDetails($request);
    }

    public static function editBookedWages($request)
    {
        $request->floor = $request->level;
        WagesDetails::updateWages($request->id,$request->toArray());
    }

    public static function deleteBookedWages($request)
    {
        WagesDetails::deleteWages($request->id);
    }

    public static function finalSubmissionWages($request)
    {
        WagesDetails::finalWagesSubmission($request);
    }

}
