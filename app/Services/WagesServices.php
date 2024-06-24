<?php

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\BlockDetails;
use App\Models\ConstructionDetails;
use App\Models\PayToDetails;
use App\Models\User;
use App\Models\WagesDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WagesServices{

    public static function bookWages($request)
    {
        $data = $request->toArray();
        $is_multiple = (count($data['book_wages'])>1)?true:false;
        foreach($data['book_wages'] as &$value){
            $value['pay_to'] = Auth::User()->first_name." ".Auth::User()->last_name;
            $value['delivery_date'] =  Carbon::parse($value['delivery_date'])->format('Y-m-d');
            if(!empty($value['apartment_id']) || !empty($value['floor_id'])){
                if($is_multiple && empty($value['sub_description_id'])){
                    throw new AppException("For booking wages sub description id is required.");
                }
                $value['floor'] = $value['level'];
                // if($value['sum'] < 0){
                //     throw new AppException("Invalid amount");
                // }
                //$value['user_id'] = User::details()->id;
                $remaining_balance = ($is_multiple)?roundOff(remainingBalanceCheckMultipleCase($value['project_id'],$value['block_id'],!empty($value['apartment_id'])?$value['apartment_id']:null,!empty($value['floor_id'])?$value['floor_id']:null,$value['main_description_id'],!empty($value['sub_description_id'])?$value['sub_description_id']:null,$value['consruction_id'])) : roundOff(remainingBalanceCheck($value['project_id'],$value['block_id'],!empty($value['apartment_id'])?$value['apartment_id']:null,!empty($value['floor_id'])?$value['floor_id']:null,$value['main_description_id'],$value['sub_description_id'],$value['consruction_id']));
                
                if((float)$value['sum'] > round($remaining_balance)){
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

    public static function getWages($request,$excel_flag = false,$date_flag=false)
    {
        $return = WagesDetails::getWagesExcelDownload($request,$excel_flag,$date_flag);
        $total_booking = 0;
        foreach($return['wages_details'] as &$value){
            $value['remaining_amount'] = ConstructionDetails::getRemaingAmountForWages($value);
            $total_booking += $value['amount'];
        }
        $download_data = WagesDetails::getWagesExcelDownload($request,$excel_flag,$date_flag);
        $records = $excel_data = [];
        $user_name = Auth::User()->first_name." ".Auth::User()->last_name;
        foreach($download_data['wages_details'] as $value){
            $records['BOOKED BY'] = $value['pay_to'];     // Coloumn A
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['BLOCK'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['LEVEL'] = $value['floor_name'];  // Coloumn F
            $records['PLOT'] = $value['plot_or_room'];     // Coloumn E
            $records['Main Description'] = $value['description_header'];      // Coloumn H
            $records['Sub Description'] = $value['sub_description_header'];      // Coloumn H
            $records['DESCRIPTION OF WORK'] = $value['description'];  // Coloumn G
            $records['Booked Quantity'] = roundOff($value['amount']);
            $records['Unit'] = $value['unit'];      // Coloumn I
            $records['Booking Date'] = Carbon::parse($value['created_at'])->format('Y-m-d');      // Coloumn I
            $records['Delivery Date'] = Carbon::parse($value['delivery_date'])->format('Y-m-d');     // Coloumn I
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        if($excel_flag){
            $return['excel_url'] = getXlsxFile($excel_data, 'Material_Booking_'.$user_name,$request['wages_date']);
        }else{
            $return['total_booking'] = $total_booking;
            $return['edit_and_delete_permission'] = 1;//(checkUserRole($request['user_id']) == 'admin')?1:0;
            //$return['wages_report_permission'] = (Auth::User()->user_role == 'admin')?true:false;
            $return['wages_report_permission'] = true;

        }



        return $return;
    }

    public static function getWagesReport($request)
    {
        $return = WagesDetails::getWagesReport($request);
        $total_booking = 0;
        foreach($return['wages_details'] as &$value){
            $value['remaining_amount'] = ConstructionDetails::getRemaingAmountForWages($value);
            $total_booking += $value['amount'];
        }
        $return['total_booking'] = $total_booking;
        $return['edit_and_delete_permission'] = 0;

        return $return;
    }

    public static function getDownloadWagesReport($request)
    {
        $return = WagesDetails::getWagesReport($request);
        $user_name = Auth::User()->first_name." ".Auth::User()->last_name;
        foreach($return['wages_details'] as $value){
            $records['BOOKED BY'] = $value['pay_to'];     // Coloumn A
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['BLOCK'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['LEVEL'] = $value['floor_name'];  // Coloumn F
            $records['PLOT'] = $value['plot_or_room'];     // Coloumn E
            $records['Main Description'] = $value['description_header'];      // Coloumn H
            $records['Sub Description'] = $value['sub_description_header'];      // Coloumn H
            $records['DESCRIPTION OF WORK'] = $value['description'];  // Coloumn G
            $records['Booked Quantity'] = roundOff($value['amount']);
            $records['Unit'] = $value['unit'];      // Coloumn I
            $records['Booking Date'] = Carbon::parse($value['created_at'])->format('Y-m-d');      // Coloumn I
            $records['Delivery Date'] = Carbon::parse($value['delivery_date'])->format('Y-m-d');     // Coloumn I
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        $excel['excel_url'] = getXlsxFile($excel_data, 'Material_Booking_Report_'.$user_name,Carbon::now());

        return $excel;
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
        $user_name = Auth::User()->first_name." ".Auth::User()->last_name;
        $records = $excel_data = [];
        foreach($download_data['wages_details'] as $value){
            $records['BOOKED BY'] = $value['pay_to'];     // Coloumn A
            //$records['Level'] = $value['level'];    // Coloumn C
            $records['BLOCK'] = BlockDetails::getBlockName($value['block_id'])->block_name?? " ";     // Coloumn D
            $records['LEVEL'] = $value['floor_name'];  // Coloumn F
            $records['PLOT'] = $value['plot_or_room'];     // Coloumn E
            $records['Main Description'] = $value['description_header'];      // Coloumn H
            $records['Sub Description'] = $value['sub_description_header'];      // Coloumn H
            $records['DESCRIPTION OF WORK'] = $value['description'];  // Coloumn G
            $records['Booked Quantity'] = roundOff($value['amount']);
            $records['Unit'] = $value['unit'];      // Coloumn I
            $records['Booking Date'] = Carbon::parse($value['created_at'])->format('Y-m-d');      // Coloumn I
            $records['Delivery Date'] = Carbon::parse($value['delivery_date'])->format('Y-m-d');     // Coloumn I
            //$records['CHECK'] = '';     // Coloumn T
            $excel_data [] = $records;
        }

        $return['excel_url'] = getXlsxFile($excel_data, 'Material_Booking_'.$user_name,date('Y_m_d_H_i_s'));

        return $return;
    }

}
