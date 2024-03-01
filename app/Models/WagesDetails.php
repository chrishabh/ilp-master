<?php

namespace App\Models;

use Carbon\Carbon;
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
        
        WagesDetails::insert($request);
    }

    public static function getWages($request,$excel_data = false)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;
        $project_id = $request['project_id'];
        $user_id = $request['user_id'];

        $return['total_records'] = WagesDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('user_id',$request['user_id'])->count('id');

        $data = WagesDetails::join('project_details','wages_details.project_id','=','project_details.id')
        ->join('block_details','wages_details.block_id','=','block_details.id')
        ->join('main_descritpions', 'main_descritpions.id', '=', 'wages_details.main_description_id')
        ->leftjoin('sub_descritpions', 'sub_descritpions.id', '=', 'wages_details.sub_description_id')
        ->leftjoin('apartment_details','wages_details.apartment_id','=','apartment_details.id')
        ->leftjoin('floors','wages_details.floor_id','=','floors.id')
        ->select('wages_details.id','wages_details.wages','wages_details.pay_to','wages_details.trade','wages_details.floor as level','wages_details.block_id',
        'wages_details.plot_or_room','wages_details.description_work','wages_details.m2_or_hours','wages_details.rate','wages_details.floor_id',
        'wages_details.sum as amount','wages_details.apartment_id','wages_details.main_description_id','wages_details.project_id','sub_description_id',
        'project_details.project_name','block_details.block_name','wages_details.apartment_id'
        ,'apartment_details.apartment_number','main_descritpions.description as description_header','sub_descritpions.sub_description as sub_description_header','floors.floor_name','wages_details.floor_id')
        ->whereNull('wages_details.deleted_at')
        ->where('wages_details.project_id',$request['project_id'])
        //->where('wages_details.block_id',$request['block_id'])
        ->where('wages_details.user_id',$request['user_id']);
        //->where('wages_details.apartment_id',$request['apartment_id'])
        if($excel_data == true){
            $latest_sunday =  date('Y-m-d'); //pp($latest_sunday);
            $data = $data->whereNull("final_submission_date")
            ->get();
        }else{
            $data = $data->whereNull('wages_details.final_submission_date')->offset($offset)->limit($noOfRecord)->get();
        }
        

        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }else{
            $return['wages_details'] = [];
        }
        return $return;
    }

    public static function getWagesExcelDownload($request,$excel_data = false,$date_flag=false)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;
        $project_id = $request['project_id'];
        $user_id = $request['user_id'];

        $return['total_records'] = WagesDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('user_id',$request['user_id'])->count('id');

        $data = WagesDetails::join('project_details','wages_details.project_id','=','project_details.id')
        ->join('block_details','wages_details.block_id','=','block_details.id')
        ->join('main_descritpions', 'main_descritpions.id', '=', 'wages_details.main_description_id')
        ->leftjoin('sub_descritpions', 'sub_descritpions.id', '=', 'wages_details.sub_description_id')
        ->leftjoin('apartment_details','wages_details.apartment_id','=','apartment_details.id')
        ->leftjoin('floors','wages_details.floor_id','=','floors.id')
        ->select('wages_details.id','wages_details.wages','wages_details.pay_to','wages_details.trade','wages_details.floor as level','wages_details.block_id',
        'wages_details.plot_or_room','wages_details.description_work','wages_details.m2_or_hours','wages_details.rate','wages_details.floor_id',
        'wages_details.sum as amount','wages_details.apartment_id','wages_details.main_description_id','wages_details.project_id','sub_description_id',
        'project_details.project_name','block_details.block_name','wages_details.apartment_id'
        ,'apartment_details.apartment_number','main_descritpions.description as description_header','sub_descritpions.sub_description as sub_description_header','floors.floor_name','wages_details.floor_id')
        ->whereNull('wages_details.deleted_at')
        ->where('wages_details.project_id',$request['project_id'])
        //->where('wages_details.block_id',$request['block_id'])
        ->where('wages_details.user_id',$request['user_id']);
        $latest_sunday =  date('Y-m-d'); //pp($latest_sunday);
        if($date_flag){
            $date = date('Y-m-d',strtotime($request['wages_date']));
            $data = $data->whereNotNull("final_submission_date")->whereRaw("cast(wages_details.created_at as date) = '$date'")->get();
        }else{
            $data = $data->whereNull("final_submission_date")->get();
            // $data = $data->whereRaw("cast(wages_details.created_at as date) = '$latest_sunday'")->get();
        }
       
        

        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }else{
            $return['wages_details'] = [];
        }
        return $return;
    }

    public static function getWagesReport($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;
        $project_id = $request['project_id'];
        $user_id = $request['user_id'];
        $date =  Carbon::parse($request['date'])->format('Y-m-d');

        $return['total_records'] = WagesDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('user_id',$request['user_id'])->whereRaw("cast(created_at as date) = '$date'")->count('id');

        $data = WagesDetails::join('project_details','wages_details.project_id','=','project_details.id')
        ->join('block_details','wages_details.block_id','=','block_details.id')
        ->join('main_descritpions', 'main_descritpions.id', '=', 'wages_details.main_description_id')
        ->leftjoin('sub_descritpions', 'sub_descritpions.id', '=', 'wages_details.sub_description_id')
        ->leftjoin('apartment_details','wages_details.apartment_id','=','apartment_details.id')
        ->leftjoin('floors','wages_details.floor_id','=','floors.id')
        ->select('wages_details.id','wages_details.wages','wages_details.pay_to','wages_details.trade','wages_details.floor as level','wages_details.block_id',
        'wages_details.plot_or_room','wages_details.description_work','wages_details.m2_or_hours','wages_details.rate','wages_details.floor_id',
        'wages_details.sum as amount','wages_details.apartment_id','wages_details.main_description_id','wages_details.project_id','sub_description_id',
        'project_details.project_name','block_details.block_name','wages_details.apartment_id'
        ,'apartment_details.apartment_number','main_descritpions.description as description_header','sub_descritpions.sub_description as sub_description_header','floors.floor_name','wages_details.floor_id')
        ->whereNull('wages_details.deleted_at')
        ->where('wages_details.project_id',$request['project_id'])
        ->where('wages_details.user_id',$request['user_id']);
        $data = $data->whereRaw("cast(wages_details.created_at as date) = '$date'")->get();
       
        
        if(count($data)>0){
            $return['wages_details'] = $data->toArray();
        }else{
            $return['wages_details'] = [];
        }
        return $return;
    }

    public static function updateWages($id,$data = [])
    {
        unset($data['level']); unset($data['old_amount']);
        return WagesDetails::whereNull('deleted_at')->where('id',$id)->update($data);
    }

    public static function deleteWages($id)
    {
        return WagesDetails::whereNull('deleted_at')->where('id',$id)->update(['deleted_at'=>date('Y-m-d')]);
    }

    public static function finalWagesSubmission($request)
    {
        $user_id = $request['user_id'];
        return WagesDetails::whereNull('deleted_at')->where('user_id',$user_id)->update(['final_submission_date'=>date('Y-m-d')]);
    }

    public static function getWagesById($id)
    {
        return WagesDetails::whereNull('deleted_at')->where('id',$id)->first();
    }
}
