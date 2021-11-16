<?php

namespace App\Models;

use App\Exceptions\AppException;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConstructionDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function getConstructionDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;
        $header = $main_response  =[];
        $sub_header = [];
        $return = [];
        $total = $total_amount_booked = 0;

        $return['total_records'] = ConstructionDetails::whereNull('deleted_at')->where('apartment_id',$request['apartment_id'])->where('project_id',$request['project_id'])->where('block_id',$request['block_id'])->count('id');

        $distinct_main_header = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
        ->select('main_descritpions.description as description_header','main_description_id')->whereNull('construction_details.deleted_at')
        ->where('construction_details.apartment_id',$request['apartment_id'])->where('construction_details.project_id',$request['project_id'])->where('construction_details.block_id',$request['block_id'])
        ->distinct()->offset($offset)->limit($noOfRecord)->get();

        foreach($distinct_main_header as $value){
            $final = $sub_final = [];
            $total = $total_amount_booked = 0;
            $final['description_header'] = $value['description_header'];

            $distinct_sub_headers = ConstructionDetails::join('sub_descritpions', 'sub_descritpions.id', '=', 'construction_details.sub_description_id')
            ->select('sub_descritpions.sub_description','sub_description_id')->whereNull('construction_details.deleted_at')
            ->where('construction_details.apartment_id',$request['apartment_id'])->where('construction_details.project_id',$request['project_id'])
            ->where('construction_details.block_id',$request['block_id'])->where('construction_details.main_description_id',$value['main_description_id'])->distinct()->get();
            
            foreach($distinct_sub_headers as $sub_header){
                $sub_final['sub_description'] = $sub_header['sub_description'];
                $sub_final['records'] = [];
                $data = ConstructionDetails::whereNull('construction_details.deleted_at')
                ->where('construction_details.apartment_id',$request['apartment_id'])->where('construction_details.project_id',$request['project_id'])
                ->where('construction_details.block_id',$request['block_id'])
                ->where('construction_details.main_description_id',$value['main_description_id'])->where('construction_details.sub_description_id',$sub_header['sub_description_id'])->get();
                foreach($data->toArray() as $records){
                    $sub_final['records'][] =  $records;
                    $total +=  $records['total'];
                    $res = explode(',',str_replace("'", "", $records['amount_booked']));
                    $total_amount_booked +=  array_sum($res);
                }

                $final['sub_description_records'][] =  $sub_final;
                $final['total'] = roundOff($total);
                $final['total_amount_booked'] = roundOff($total_amount_booked);
               
            }

            $main_response [] = $final;
           
        }

        $return['construction_details'] = $main_response;
        return $return;
    }

    public static function getDescriptionWork($request)
    {
        // $noOfRecord = $request['no_of_records'] ?? 10;
        // $current_page = $request['page_number'] ?? 1;
        // $offset = ($current_page*$noOfRecord)-$noOfRecord;
        $return = $final = $response = $sub_final = $records =[];

        $return['total_records'] = ConstructionDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('block_id',$request['block_id'])->distinct()->count('main_description_id');

        $data = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
        ->select('construction_details.main_description_id','construction_details.apartment_id','main_descritpions.description as description_header',DB::raw("CASE WHEN sum(construction_details.total) IS NULL THEN 0 WHEN sum(construction_details.amount_booked) IS NULL THEN ROUND(sum(construction_details.total),2) ELSE ROUND(sum(construction_details.total)-sum(construction_details.amount_booked),2) END as remaining_booking_amount"))->whereNull('construction_details.deleted_at')
        ->where('construction_details.project_id',$request['project_id'])
        ->where('construction_details.block_id',$request['block_id'])
        ->whereIn('construction_details.apartment_id',$request['apartment_id'])
        ->groupBy('description_header','construction_details.main_description_id','construction_details.apartment_id')->get();

        if(count($data)>0){
            $records = $data->toArray();
        }

        foreach($records as $value){
            $response[$value['description_header']]['description_header'] = $value['description_header'];
            $sub_final['main_description_id'] = $value['main_description_id'];
            $sub_final['apartment_id'] = $value['apartment_id'];
            $sub_final['remaining_booking_amount'] = $value['remaining_booking_amount'];
            $response[$value['description_header']]['records'][] =  $sub_final;

        }
        $main_description = MainDescritpion::getDistinctDescription();
        foreach($main_description as $value){
            if(isset($response[$value['description_header']])){
                $final[] =  $response[$value['description_header']];
            }
        }

        $return['description_work_details'] = $final;

        return $return;
    }

    public static function addWagesBookValue($request)
    {
        $return = ConstructionDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('block_id',$request['block_id'])->where('main_description_id',$request['main_description_id'])->where('apartment_id',$request['apartment_id'])->first();

        if(isset($return['amount_booked']))
        {
            if(!empty($return['amount_booked'])){
                $amount_booked = str_replace("'", "", $return['amount_booked']).",".$request['sum'];
            }else{
                $amount_booked = $request['sum'];
            }
        }else{
            $amount_booked = $request['sum'];
        }
        if(isset($return['wages'])){
            if(!empty($return['wages'])){
                $wages = str_replace("'", "", $return['wages']).",".$request['wages'];
            }else{
                $wages = $request['wages'];
            }
        } else {
            $wages = $request['wages'];
        }
        if(isset($return['name'])){
            if(!empty($return['name'])){
                $name = str_replace("'", "", $return['name']).",".$request['pay_to'];
            }else{
                $name = $request['pay_to'];
            }
        } else {
            $name = $request['pay_to'];
        }
        DB::select("UPDATE construction_details SET amount_booked = '$amount_booked', wages = '$wages' ,`name` = '$name' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$request['project_id']." and block_id = ".$request['block_id']." and main_description_id =".$request['main_description_id']." and apartment_id =".$request['apartment_id']." ) as cunst)");
    }

    public static function addConstructionDetails($request)
    {
        $records = [];
        foreach($request['construction_details'] as $value)
        {
            $data['description'] = $value['description'];
            $data['area'] = $value['area'];
            $data['unit'] = $value['unit'];
            $data['lab_rate'] = $value['lab_rate'];
            $data['total'] = $value['total'];
            $data['amount_booked'] = $value['amount_booked'];
            $data['name'] = $value['name'];
            $data['wages'] = $value['wages'];
            $data['quantity'] = $value['quantity'];
            $data['booking_description'] = $value['booking_description'];
            $data['floor'] = $value['floor'];
            $data['main_description_id'] = $request['main_description_id'];
            $data['sub_description_id'] = $request['sub_description_id'];
            $data['apartment_id'] = $request['apartment_id'];
            $data['block_id'] = $request['block_id'];
            $data['project_id'] = $request['project_id'];

            $records [] = $data;
        }

        try{
            return DB::table('construction_details')->insert($records);
        } catch (Exception $e) {
            throw new AppException('Something went wrong');
        }
    }

    public static function updateConstructionDetails($id,$data)
    {
        return ConstructionDetails::WhereNull('deleted_at')->where('id',$id)->update($data);
    }

    public static function getConstructionDetailsForProject($project_id)
    {
        $return = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
        ->join('sub_descritpions', 'sub_descritpions.id', '=', 'construction_details.sub_description_id')
        ->join('apartment_details', 'apartment_details.id', '=', 'construction_details.apartment_id')
        ->select('main_descritpions.description as ','')
        ->WhereNull('construction_details.deleted_at')->where('construction_details.project_id',$project_id)->get();

        if(count($return)>0){
            return $return->toArray();
        }

        return [];
    }
}
