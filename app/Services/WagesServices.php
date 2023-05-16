<?php

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\BlockDetails;
use App\Models\ConstructionDetails;
use App\Models\PayToDetails;
use App\Models\User;
use App\Models\WagesDetails;
use Carbon\Carbon;

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
                $remaining_balance = ($is_multiple)?roundOff(remainingBalanceCheckMultipleCase($value['project_id'],$value['block_id'],!empty($value['apartment_id'])?$value['apartment_id']:null,!empty($value['floor_id'])?$value['floor_id']:null,$value['main_description_id'],!empty($value['sub_description_id'])?$value['sub_description_id']:null)) : roundOff(remainingBalanceCheck($value['project_id'],$value['block_id'],!empty($value['apartment_id'])?$value['apartment_id']:null,!empty($value['floor_id'])?$value['floor_id']:null,$value['main_description_id'],$value['sub_description_id']));
                
                if((float)$value['sum'] > $remaining_balance){
                    if($is_multiple){
                        continue;
                    }
                    throw new AppException("For booking wages Booking Amount is Insufficient.");
                }else{
                    unset($value['level']);
                    WagesDetails::bookWages($value);
                    ConstructionDetails::addWagesBookValue($value,true);
                }  


            }else{
                throw new AppException("For booking wages apartment or floor is required.");
            }
        }
        
    }

    public static function getWages($request,$excel_flag = false)
    {
        $return = WagesDetails::getWagesExcelDownload($request,$excel_flag);
        $total_booking = 0;
        foreach($return['wages_details'] as &$value){
            $value['remaining_amount'] = ConstructionDetails::getRemaingAmountForWages($value);
            $total_booking += $value['amount'];
        }
        $download_data = WagesDetails::getWagesExcelDownload($request,$excel_flag);
        $records = $excel_data = [];
        foreach($download_data['wages_details'] as $value){
            $records['Subcontractor Ref'] = PayToDetails::getPayToCode($value['pay_to'])->pay_to_code??" ";
            $records['PAY TO:'] = $value['pay_to'];     // Coloumn A
            $records['TRADE'] = $value['trade'];    // Coloumn B
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['BLOCK'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['LEVEL'] = $value['floor_name'];  // Coloumn F
            $records['PLOT/ROOM'] = $value['plot_or_room'];     // Coloumn E
            $records['Main Description'] = $value['description_header'];      // Coloumn H
            $records['Sub Description'] = $value['sub_description_header'];      // Coloumn H
            $records['DESCRIPTION OF WORK'] = $value['description_work'];  // Coloumn G
            $rate = ConstructionDetails::getRates($value['project_id'],$value['block_id'],$value['apartment_id'],$value['floor_id'],$value['main_description_id'],$value['sub_description_id']);
            $records['Quantity'] = ($rate != '0')?roundOff($value['amount']/$rate):'';
            $records['Unit'] = $value['m2_or_hours'];      // Coloumn I
            $records['RATE'] =    $rate;  // Coloumn J
            $records['Booking Amount'] = roundOff($value['amount']);     // Coloumn K
           
            $records['Instruction Req'] = '';        // Coloumn L
            $records['Instruction Recd'] = '';        // Coloumn M
            //$records[' '] = '';         // Coloumn 
            $records['Approved (surveyor)'] = roundOff($value['amount']);       // Coloumn N
            $records['Difference'] = '';        // Coloumn O
            $records['Surveyor comments'] = '';     // Coloumn P
            $records['Supervisor Comment'] = '';     // Coloumn P
            $records['Measured'] = "£".roundOff($value['amount']);       // Coloumn Q
            $records['Variation'] = '';     // Coloumn S
            $records['Possible VO'] = '';       // Coloumn R
          
            $records['Non Rec'] ='';      // Coloumn T
            $records['Mgt'] ='';      // Coloumn T
            $records['CHECK'] ='';      // Coloumn T
            $records['Wages No.'] =$value['wages'];
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        if($excel_flag){
            $return['excel_url'] = getXlsxFile($excel_data, 'Wages_Booking_'.$request['wages_number']);
        }else{
            $return['total_booking'] = $total_booking;
            $return['edit_and_delete_permission'] = 1;//(checkUserRole($request['user_id']) == 'admin')?1:0;
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

    public static function deletePayToDetails($request)
    {
       return PayToDetails::deletePayToDetails($request);
    }

    public static function uploadPayToDetails($request)
    {
        if (isset($_FILES) && !empty($_FILES['request']['name']['file'])) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 180);
            $dir_name =  $_SERVER['DOCUMENT_ROOT']."/storage/pay_to_files"."//";
            //$dir_name =  env('VIDEOS_PATH')."/storage"."//";
            if (!is_dir($dir_name)) {
                @mkdir($dir_name, "0777", true);
            }

            $current_timestamp  = Carbon::now()->timestamp;
            $video_saved_name = $current_timestamp . $_FILES['request']['name']['file'];
            

            $video_data['video_name'] =  $_FILES['request']['name']['file'];
            $video_data['video_path'] = $dir_name.$video_saved_name;
            $request->file->move($dir_name, $video_saved_name);
            
            PayToDetails::importPayToDetails($video_data['video_path']);
        }
    }

    public static function editBookedWages($request)
    {
        $request->floor = $request->level;
        $data = WagesDetails::getWagesById($request->id)->toArray();
        $request->sub_description_id = $data['sub_description_id'];
        $dummy_array['sub_description_id'] = $data['sub_description_id'];
        if((float)$request->sum > roundOff(remainingBalanceCheck($request->project_id,$request->block_id,!empty($request->apartment_id)?$request->apartment_id:null,!empty($request->floor_id)?$request->floor_id:null,$request->main_description_id,$request->sub_description_id) + $request->old_amount)){
            throw new AppException("Booking Amount is Insufficient.");
        
        }else{
            WagesDetails::updateWages($request->id,$request->toArray());
            ConstructionDetails::updateConstructionEditWagesCase(array_merge($request->toArray(),$dummy_array));
        }
    }

    public static function deleteBookedWages($request)
    {
        ConstructionDetails::updateConstructionDeleteWagesCase($request->id);
        WagesDetails::deleteWages($request->id);
    }

    public static function finalSubmissionWages($request)
    {
        

        $download_data = WagesDetails::getWages($request,true);
        WagesDetails::finalWagesSubmission($request);
        $records = $excel_data = [];
        foreach($download_data['wages_details'] as $value){
            $records['Subcontractor Ref'] = PayToDetails::getPayToCode($value['pay_to'])->pay_to_code??" ";
            $records['PAY TO:'] = $value['pay_to'];     // Coloumn A
            $records['TRADE'] = $value['trade'];    // Coloumn B
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['BLOCK'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['LEVEL'] = $value['floor_name'];  // Coloumn F
            $records['PLOT/ROOM'] = $value['plot_or_room'];     // Coloumn E
            $records['Main Description'] = $value['description_header'];      // Coloumn H
            $records['Sub Description'] = $value['sub_description_header'];      // Coloumn H
            $records['DESCRIPTION OF WORK'] = $value['description_work'];  // Coloumn G
            $rate = ConstructionDetails::getRates($value['project_id'],$value['block_id'],$value['apartment_id'],$value['floor_id'],$value['main_description_id'],$value['sub_description_id']);
            $records['Quantity'] = ($rate != '0')?roundOff($value['amount']/$rate):'';
            $records['Unit'] = $value['m2_or_hours'];      // Coloumn I
            $records['RATE'] =    $rate;  // Coloumn J
            $records['Booking Amount'] = roundOff($value['amount']);     // Coloumn K
           
            $records['Instruction Req'] = '';        // Coloumn L
            $records['Instruction Recd'] = '';        // Coloumn M
            //$records[' '] = '';         // Coloumn 
            $records['Approved (surveyor)'] = roundOff($value['amount']);       // Coloumn N
            $records['Difference'] = '';        // Coloumn O
            $records['Surveyor comments'] = '';     // Coloumn P
            $records['Supervisor Comment'] = '';     // Coloumn P
            $records['Measured'] = "£".roundOff($value['amount']);       // Coloumn Q
            $records['Variation'] = '';     // Coloumn S
            $records['Possible VO'] = '';       // Coloumn R
          
            $records['Non Rec'] ='';      // Coloumn T
            $records['Mgt'] ='';      // Coloumn T
            $records['CHECK'] ='';      // Coloumn T
            $records['Wages No.'] =$value['wages'];
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        $return['excel_url'] = getXlsxFile($excel_data, 'Wages_Booking_'.$request['wages_number']);

        return $return;
    }

}
