<?php

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\BlockDetails;
use App\Models\ConstructionDetails;
use App\Models\PayToDetails;
use App\Models\User;
use App\Models\WagesDetails;

class WagesServices{

    public static function bookWages($request)
    {
        $data = $request->toArray();
        $is_multiple = (count($data['book_wages'])>1)?true:false;
        foreach($data['book_wages'] as &$value){
            if(!empty($value['apartment_id']) || !empty($value['floor_id'])){
                if($is_multiple && empty($value['sub_description_id'])){
                    throw new AppException("For booking wages sub description id is required.");
                }
                $value['floor'] = $value['level'];
                // if($value['sum'] < 0){
                //     throw new AppException("Invalid amount");
                // }
                //$value['user_id'] = User::details()->id;
                $remaining_balance = ($is_multiple)?roundOff(remainingBalanceCheckMultipleCase($value['project_id'],$value['block_id'],!empty($value['apartment_id'])?$value['apartment_id']:null,!empty($value['floor_id'])?$value['floor_id']:null,$value['main_description_id'],!empty($value['sub_description_id'])?$value['sub_description_id']:null)) : roundOff(remainingBalanceCheck($value['project_id'],$value['block_id'],!empty($value['apartment_id'])?$value['apartment_id']:null,!empty($value['floor_id'])?$value['floor_id']:null,$value['main_description_id']));
                if((float)$value['sum'] > $remaining_balance){
                    throw new AppException("For booking wages Booking Amount is Insufficient.");
                }else{
                    unset($value['level']);
                    WagesDetails::bookWages($value);
                    ConstructionDetails::addWagesBookValue($value,$is_multiple);
                }  


            }else{
                throw new AppException("For booking wages apartment or floor is required.");
            }
        }
        
    }

    public static function getWages($request,$excel_flag = false)
    {
        $return = WagesDetails::getWages($request,$excel_flag);
        foreach($return['wages_details'] as &$value){
            $value['remaining_amount'] = ConstructionDetails::getRemaingAmountForWages($value);
        }
        $download_data = WagesDetails::getWages($request,$excel_flag);
        $records = $excel_data = [];
        foreach($download_data['wages_details'] as $value){
            $records['Pay Code'] = PayToDetails::getPayToCode($value['pay_to'])->pay_to_code??" ";
            $records['Pay To:'] = $value['pay_to'];     // Coloumn A
            $records['Trade'] = $value['trade'];    // Coloumn B
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['Block'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['Plot/room'] = $value['plot_or_room'];     // Coloumn E
            $records['Level'] = $value['floor_name']; 
            $records['Description of work'] = $value['description_work'];       // Coloumn F
            $records['m2 (or hours)'] = $value['m2_or_hours'];      // Coloumn G
            $records['Rate'] = $value['rate'];      // Coloumn H
            $records['Booking Amount'] = roundOff($value['amount']);     // Coloumn I
            $records['Instruction required (y/n)'] = '';        // Coloumn J
            $records['Instruction received (y/n)'] = '';        // Coloumn K
            //$records[' '] = '';         // Coloumn L
            $records['Approved'] = roundOff($value['amount']);       // Coloumn M
            $records['Difference'] = '';        // Coloumn N
            $records['Surveyor comments'] = '';     // Coloumn O
            $records['measured'] = "£".roundOff($value['amount']);       // Coloumn P
            $records['Possible VO'] = '';       // Coloumn Q
            $records['variation'] = '';     // Coloumn R
            $records['non recov'] ='';      // Coloumn S
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        if($excel_flag){
            $return['excel_url'] = getXlsxFile($excel_data, 'Wages_Booking_'.$request['wages_number']);
        }



        return $return;
    }

    // public static function getWagesExcel($request)
    // {
    //     $return = WagesDetails::getWages($request,1);

    //     $download_data = WagesDetails::getWages($request,true);
       
    //     $i = 0;
    //     $records = $excel_data = [];
    //     foreach($download_data['wages_details'] as $value){

    //         $records['Pay To:'] = $value['pay_to'];
    //         $records['Trade'] = $value['trade'];
    //         $records['Level'] = $value['level'];
    //         $records['Block'] = $value['block_id'];
    //         $records['Plot/room'] = $value['plot_or_room'];
    //         $records['Description of work'] = $value['description_work'];
    //         $records['m2 (or hours)'] = $value['m2_or_hours'];
    //         $records['Rate'] = $value['rate'];
    //         $records['Sum'] = $value['sum'];

    //         if($i%2 == 0){
    //             $excel_data ['I.L.P.1.0'][] = $records;
    //         }else{
    //             $excel_data ['I.L.P.2.0'][] = $records;
    //         }
            
           
    //         $i++;
    //     }
    //     $return['excel_url'] = getXlsxFiles($excel_data, 'Wages_Booking');

    //     return $return;
    // }

    public static function addPayToDetails($request)
    {
       return PayToDetails::addPayToDetails($request);
    }

    public static function editBookedWages($request)
    {
        $request->floor = $request->level;
        if((float)$request->sum > roundOff(remainingBalanceCheck($request->project_id,$request->block_id,!empty($request->apartment_id)?$request->apartment_id:null,!empty($request->floor_id)?$request->floor_id:null,$request->main_description_id) + $request->old_amount)){
            throw new AppException("Booking Amount is Insufficient.");
        
        }else{
            WagesDetails::updateWages($request->id,$request->toArray());
            ConstructionDetails::updateConstructionEditWagesCase($request->toArray());
        }
    }

    public static function deleteBookedWages($request)
    {
        ConstructionDetails::updateConstructionDeleteWagesCase($request->id);
        WagesDetails::deleteWages($request->id);
    }

    public static function finalSubmissionWages($request)
    {
        WagesDetails::finalWagesSubmission($request);

        $download_data = WagesDetails::getWages($request,true);
        $records = $excel_data = [];
        foreach($download_data['wages_details'] as $value){
            $records['Pay Code'] = PayToDetails::getPayToCode($value['pay_to'])->pay_to_code??" ";
            $records['Pay To:'] = $value['pay_to'];     // Coloumn A
            $records['Trade'] = $value['trade'];    // Coloumn B
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['Block'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['Plot/room'] = $value['plot_or_room'];     // Coloumn E
            $records['Level'] = $value['floor_name']; 
            $records['Description of work'] = $value['description_work'];       // Coloumn F
            $records['m2 (or hours)'] = $value['m2_or_hours'];      // Coloumn G
            $records['Rate'] = $value['rate'];      // Coloumn H
            $records['Booking Amount'] = roundOff($value['amount']);     // Coloumn I
            $records['Instruction required (y/n)'] = '';        // Coloumn J
            $records['Instruction received (y/n)'] = '';        // Coloumn K
            //$records[' '] = '';         // Coloumn L
            $records['Approved'] = roundOff($value['amount']);       // Coloumn M
            $records['Difference'] = '';        // Coloumn N
            $records['Surveyor comments'] = '';     // Coloumn O
            $records['measured'] = "£".roundOff($value['amount']);       // Coloumn P
            $records['Possible VO'] = '';       // Coloumn Q
            $records['variation'] = '';     // Coloumn R
            $records['non recov'] ='';      // Coloumn S
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        $return['excel_url'] = getXlsxFile($excel_data, 'Wages_Booking_'.$request['wages_number']);

        return $return;
    }

}
