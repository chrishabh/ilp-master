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
                    $total_amount_booked +=  $records['amount_booked'];
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
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;
        $return = [];

        $return['total_records'] = ConstructionDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('block_id',$request['block_id'])->distinct()->count('main_description_id');

        $data = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
        ->select('construction_details.main_description_id','main_descritpions.description as description_header',DB::raw("CASE WHEN sum(construction_details.total)-sum(construction_details.amount_booked) IS NULL THEN 0  ELSE sum(construction_details.total)-sum(construction_details.amount_booked) END as total"))->whereNull('construction_details.deleted_at')
        ->where('construction_details.project_id',$request['project_id'])
        ->where('construction_details.block_id',$request['block_id'])
        ->where('construction_details.apartment_id',$request['apartment_id'])
        ->groupBy('description_header','construction_details.main_description_id')->offset($offset)->limit($noOfRecord)->get();


        if(count($return)>0){
            $return['description_work_details'] = $data->toArray();
        }
        return $return;
    }

    public static function addWagesBookValue($request)
    {
        DB::select("UPDATE construction_details SET amount_booked = ".$request['sum']." WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$request['project_id']." and block_id = ".$request['block_id']." and main_description_id =".$request['main_description_id']." and apartment_id =".$request['apartment_id'].") as cunst)");
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
}
