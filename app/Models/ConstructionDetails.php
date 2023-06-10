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
        $apartment_id = $request['apartment_id']??null;
        $floor_id = $request['floor_id']??null;

        $return['total_records'] = ConstructionDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('block_id',$request['block_id']);
        if(!empty($apartment_id)){
            $return['total_records'] = $return['total_records']->where('apartment_id',$request['apartment_id'])->count('id');
        }else{
            $return['total_records'] = $return['total_records']->where('floor_id',$floor_id)->whereNull('apartment_id')->count('id');
        }


        $distinct_main_header = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
        ->select('main_descritpions.description as description_header','main_description_id')->whereNull('construction_details.deleted_at')
        ->where('construction_details.project_id',$request['project_id'])->where('construction_details.block_id',$request['block_id']);
        if(!empty($apartment_id)){
            $distinct_main_header = $distinct_main_header->where('construction_details.apartment_id',$request['apartment_id'])->distinct()->offset($offset)->limit($noOfRecord)->get();
        }else{
            $distinct_main_header = $distinct_main_header->whereNull('construction_details.apartment_id')->where('construction_details.floor_id',$request['floor_id'])->distinct()->offset($offset)->limit($noOfRecord)->get();
        }
        

        foreach($distinct_main_header as $value){
            $final = $sub_final = [];
            $total = $total_amount_booked = 0;
            $final['description_header'] = $value['description_header'];

            $distinct_sub_headers = ConstructionDetails::join('sub_descritpions', 'sub_descritpions.id', '=', 'construction_details.sub_description_id')
            ->select('sub_descritpions.sub_description','sub_description_id')->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$request['project_id'])
            ->where('construction_details.block_id',$request['block_id'])->where('construction_details.main_description_id',$value['main_description_id']);
            if(!empty($apartment_id)){
                $distinct_sub_headers = $distinct_sub_headers->where('construction_details.apartment_id',$request['apartment_id'])->distinct()->get();
            }else{
                $distinct_sub_headers = $distinct_sub_headers->whereNull('construction_details.apartment_id')->where('construction_details.floor_id',$request['floor_id'])->distinct()->get();
            }
            
            foreach($distinct_sub_headers as $sub_header){
                $sub_final['sub_description'] = $sub_header['sub_description'];
                $sub_final['records'] = [];
                $sub_total = 0;
                $sub_amount_booked = 0;
                $data = ConstructionDetails::whereNull('construction_details.deleted_at')
                ->where('construction_details.project_id',$request['project_id'])
                ->where('construction_details.block_id',$request['block_id'])
                ->where('area','>','0')
                ->where('construction_details.main_description_id',$value['main_description_id'])->where('construction_details.sub_description_id',$sub_header['sub_description_id']);
                if(!empty($apartment_id)){
                    $data = $data->where('construction_details.apartment_id',$request['apartment_id'])->get();
                }else{
                    $data = $data->whereNull('construction_details.apartment_id')->where('construction_details.floor_id',$request['floor_id'])->get();
                }
                
                foreach($data->toArray() as $records){
                    $sub_final['records'][] =  $records;
                    $total += floatval(preg_replace('/[^\d.]/', '',$records['total']));
                    $sub_total += floatval(preg_replace('/[^\d.]/', '',$records['total']));
                    $res = explode(',',str_replace("'", "", $records['amount_booked']));
                    $total_amount_booked +=  array_sum($res);
                    $sub_amount_booked +=  array_sum($res);
                }
                $sub_final['sub_total'] = $sub_total;
                $sub_final['sub_total_amount_booked'] = $sub_amount_booked;
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

        $apartment_id = $request['apartment_id']??[];


        $return['total_records'] = ConstructionDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('block_id',$request['block_id'])->distinct()->count('main_description_id');

        if(count($apartment_id)>0){
            $data = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
            ->select('construction_details.main_description_id','construction_details.apartment_id','construction_details.floor_id','main_descritpions.description as description_header',DB::raw("CASE WHEN sum(construction_details.total) IS NULL THEN 0 ELSE ROUND(sum(construction_details.total),2) END as remaining_booking_amount"))->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$request['project_id'])
            ->where('construction_details.block_id',$request['block_id']);
        }else{
            $data = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
            ->select('construction_details.main_description_id','construction_details.floor_id','main_descritpions.description as description_header',DB::raw("CASE WHEN sum(construction_details.total) IS NULL THEN 0 ELSE ROUND(sum(construction_details.total),2) END as remaining_booking_amount"))->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$request['project_id'])
            ->where('construction_details.block_id',$request['block_id']);
        }

        if(count($apartment_id)>0){
            $data = $data->groupBy('description_header','construction_details.main_description_id','construction_details.apartment_id','construction_details.floor_id')->whereIn('construction_details.apartment_id',$request['apartment_id'])->get();
        }else{
            $data = $data->groupBy('description_header','construction_details.main_description_id','construction_details.floor_id',)->whereNull('construction_details.apartment_id')->whereIn('construction_details.floor_id',$request['floor_id'])->get();
        }
        

        if(count($data)>0){
            $records = $data->toArray();
        }
        $array  =   [];
        foreach($records as $value){
            $response[$value['description_header']]['description_header'] = $value['description_header'];

            $sub_data = ConstructionDetails::join('sub_descritpions', 'sub_descritpions.id', '=', 'construction_details.sub_description_id')
                    ->select('construction_details.sub_description_id','sub_descritpions.sub_description as sub_description_header',DB::raw("CASE WHEN sum(construction_details.total) IS NULL THEN 0 ELSE ROUND(sum(REPLACE(construction_details.total,',','')),2) END as remaining_booking_amount"))->whereNull('construction_details.deleted_at')
                    ->where('construction_details.project_id',$request['project_id'])->where('construction_details.main_description_id',$value['main_description_id'])
                    ->where('construction_details.block_id',$request['block_id']);
                if(count($apartment_id)>0){
                    $sub_data = $sub_data->where('construction_details.apartment_id',$value['apartment_id']);
                }else{
                    $sub_data = $sub_data->where('construction_details.floor_id',$value['floor_id']);
                }
                    
                $sub_data = $sub_data->groupBy('sub_description_header','construction_details.sub_description_id')->get();

            if(count($data)>0){
                $sub_records = $sub_data->toArray();
            }
            $sub = [];//pp($sub_records);
            foreach($sub_records as $sub_value)
            {$sub_final = [];
                $total_sum = ConstructionDetails::select(DB::raw("CASE WHEN sum(construction_details.total) IS NULL THEN 0 ELSE ROUND(sum(REPLACE(construction_details.total,',','')),2) END as remaining_booking_amount"))->whereNull('construction_details.deleted_at')
                ->where('construction_details.project_id',$request['project_id'])->where('construction_details.main_description_id',$value['main_description_id'])
                ->where('construction_details.block_id',$request['block_id']);
                if(count($apartment_id)>0){
                    $total_sum = $total_sum->whereIn('construction_details.apartment_id',$request['apartment_id']);
                }else{
                    $total_sum = $total_sum->whereIn('construction_details.floor_id',$request['floor_id']);
                }
                
                $total_sum = $total_sum->where('construction_details.sub_description_id',$sub_value['sub_description_id'])->get();

                $sub_response[$value['description_header']][$sub_value['sub_description_header']]['sub_description_header'] = $sub_value['sub_description_header'];
                $sub_final['main_description_id'] = $value['main_description_id'];
                $sub_final['sub_description_id'] = $sub_value['sub_description_id'];
                if(count($apartment_id)>0){
                    //$sub_description []
                    $sub_final['apartment_id'] = $value['apartment_id'];
                    $sub_final['floor_id'] = $value['floor_id'];
                    $booked_amount = ConstructionDetails::select('amount_booked')->whereNull('construction_details.deleted_at')
                    ->where('construction_details.project_id',$request['project_id'])->where('construction_details.main_description_id',$value['main_description_id'])
                    ->where('construction_details.sub_description_id',$sub_value['sub_description_id'])
                    ->where('construction_details.block_id',$request['block_id'])->where('construction_details.apartment_id',$value['apartment_id'])->get();
                    $total_amount_booked = 0;
                    if(count($booked_amount)>0){
                        foreach($booked_amount->toArray() as $booked_amount_value){
                            $res = explode(',',str_replace("'", "", $booked_amount_value['amount_booked']));
                            $total_amount_booked +=  array_sum($res);
                        }
                    }
                }else{
                    $sub_final['floor_id'] = $value['floor_id'];
                    $booked_amount = ConstructionDetails::select('amount_booked')->whereNull('construction_details.deleted_at')
                    ->where('construction_details.project_id',$request['project_id'])->where('construction_details.main_description_id',$value['main_description_id'])
                    ->where('construction_details.sub_description_id',$sub_value['sub_description_id'])
                    ->where('construction_details.block_id',$request['block_id'])->whereNull('construction_details.apartment_id')->where('construction_details.floor_id',$value['floor_id'])->get();
                    $total_amount_booked = 0;
                    if(count($booked_amount)>0){
                        foreach($booked_amount->toArray() as $booked_amount_value){
                            $res = explode(',',str_replace("'", "", $booked_amount_value['amount_booked']));
                            $total_amount_booked +=  array_sum($res);
                        }
                    }
                }
                
                $sub_final['remaining_booking_amount'] = (($sub_value['remaining_booking_amount']-$total_amount_booked) < 0)?0:$sub_value['remaining_booking_amount']-$total_amount_booked;
                //$sub_final['total'] = $value['remaining_booking_amount'];
                //$response[$value['description_header']]['total_sum'] += $value['remaining_booking_amount'];
                $sub_response[$value['description_header']][$sub_value['sub_description_header']]['sub_records'][] = $sub_final;
                $sub_response[$value['description_header']][$sub_value['sub_description_header']]['sub_total'] = count($total_sum)>0 ? ($total_sum->toArray()[0]['remaining_booking_amount']??0) : 0;
                $sub[$value['description_header']] [] = $sub_response[$value['description_header']][$sub_value['sub_description_header']];

            }
            $array[$value['description_header']][] = $value['remaining_booking_amount'];
            $response[$value['description_header']]['records'] =   $sub[$value['description_header']];

        }
        $main_description = MainDescritpion::getDistinctDescription();
        foreach($main_description as $value){
            if(isset($response[$value['description_header']])){
                if(isset($array[$value['description_header']])){
                    $response[$value['description_header']]['total'] = array_sum($array[$value['description_header']]);
                } else {
                    $response[$value['description_header']]['total'] = 0;
                }
                
                $final[] =  $response[$value['description_header']];
            }
        }

        $return['description_work_details'] = $final;

        return $return;
    }

    public static function addWagesBookValue($request,$is_multiple)
    {
        $apartment_id = $request['apartment_id']??null;
        $floor_id = $request['floor_id']??null;
        $return = ConstructionDetails::whereNull('deleted_at')->where('project_id',$request['project_id'])->where('block_id',$request['block_id'])->where('main_description_id',$request['main_description_id']);
        
        if($is_multiple){
            $return = $return->where('sub_description_id',$request['sub_description_id']);
        }

        if(!empty($apartment_id)){
            $return = $return->where('apartment_id',$request['apartment_id'])->first();
        }else{
            $return = $return->where('floor_id',$request['floor_id'])->first();
        }
        

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

        if(!empty($apartment_id)){
            if($is_multiple){
                DB::select("UPDATE construction_details SET amount_booked = '$amount_booked', wages = '$wages' ,`name` = '$name' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$request['project_id']." and block_id = ".$request['block_id']." and main_description_id =".$request['main_description_id']." and sub_description_id =".$request['sub_description_id']." and apartment_id =".$request['apartment_id']." ) as cunst)");
            }else{
                DB::select("UPDATE construction_details SET amount_booked = '$amount_booked', wages = '$wages' ,`name` = '$name' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$request['project_id']." and block_id = ".$request['block_id']." and main_description_id =".$request['main_description_id']." and apartment_id =".$request['apartment_id']." ) as cunst)");
            }
            
        }else{
            if($is_multiple){
                DB::select("UPDATE construction_details SET amount_booked = '$amount_booked', wages = '$wages' ,`name` = '$name' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$request['project_id']." and block_id = ".$request['block_id']." and main_description_id =".$request['main_description_id']." and sub_description_id =".$request['sub_description_id']." and floor_id =".$request['floor_id']." ) as cunst)");
            }else{
                DB::select("UPDATE construction_details SET amount_booked = '$amount_booked', wages = '$wages' ,`name` = '$name' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$request['project_id']." and block_id = ".$request['block_id']." and main_description_id =".$request['main_description_id']." and floor_id =".$request['floor_id']." ) as cunst)");
            }
            
        }
       
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

    public static function getConstructionDetailsForProject($project_id,$block_id = [])
    {

        if(count($block_id)>0){
            $return = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
            ->join('sub_descritpions', 'sub_descritpions.id', '=', 'construction_details.sub_description_id')
            ->leftjoin('apartment_details', 'apartment_details.id', '=', 'construction_details.apartment_id')
            ->leftjoin('floors', 'floors.id', '=', 'construction_details.floor_id')
            ->leftjoin('block_details', 'block_details.id', '=', 'construction_details.block_id')
            ->leftjoin('project_details', 'project_details.id', '=', 'construction_details.project_id')
            ->select('block_details.block_name as Block','floors.floor_name as Level','apartment_details.apartment_number as Plot','main_descritpions.description as Main Description','sub_descritpions.sub_description as Sub Description','construction_details.description as Description','construction_details.area as Area','construction_details.unit as Unit','construction_details.lab_rate as Rate'
            ,'construction_details.total as Total','construction_details.amount_booked as Amount','construction_details.name as Pay To:','construction_details.wages as Wages No.','construction_details.quantity as Qty.','construction_details.booking_description as Description of work','project_details.project_name as project_name')
            ->WhereNull('construction_details.deleted_at')->where('construction_details.project_id',$project_id)->whereIn('construction_details.block_id',$block_id)->get();
    
            if(count($return)>0){
                return $return->toArray();
            }
        }else {
            $return = ConstructionDetails::join('main_descritpions', 'main_descritpions.id', '=', 'construction_details.main_description_id')
            ->join('sub_descritpions', 'sub_descritpions.id', '=', 'construction_details.sub_description_id')
            ->leftjoin('apartment_details', 'apartment_details.id', '=', 'construction_details.apartment_id')
            ->leftjoin('floors', 'floors.id', '=', 'construction_details.floor_id')
            ->leftjoin('block_details', 'block_details.id', '=', 'construction_details.block_id')
            ->leftjoin('project_details', 'project_details.id', '=', 'construction_details.project_id')
            ->select('block_details.block_name as Block','floors.floor_name as Level','apartment_details.apartment_number as Plot','main_descritpions.description as Main Description','sub_descritpions.sub_description as Sub Description','construction_details.description as Description','construction_details.area as Area','construction_details.unit as Unit','construction_details.lab_rate as Rate'
            ,'construction_details.total as Total','construction_details.amount_booked as Amount','construction_details.name as Pay To:','construction_details.wages as Wages No.','construction_details.quantity as Qty.','construction_details.booking_description as Description of work','project_details.project_name as project_name')
            ->WhereNull('construction_details.deleted_at')->where('construction_details.project_id',$project_id)->get();
    
            if(count($return)>0){
                return $return->toArray();
            }
        }
       

        return [];
    }

    public static function deleteProjectConstructionDetails($Project_id)
    {
        return ConstructionDetails::where('project_id',$Project_id)->update(['deleted_at'=>date('Y-m-d')]);
    }

    public static function getRemaingAmountForWages($data){

        $total_amount_booked = $remaining_amount = 0;
        $total = ConstructionDetails::select(DB::raw('SUM(total) as total_amount'))
        ->where('main_description_id',$data['main_description_id'])->where('project_id',$data['project_id'])
        ->where('apartment_id',$data['apartment_id'])->whereNull('deleted_at')->get();

        $booked_amount = ConstructionDetails::select('amount_booked')
        ->where('main_description_id',$data['main_description_id'])->where('project_id',$data['project_id'])
        ->where('apartment_id',$data['apartment_id'])->whereNull('deleted_at')->get();

        if(count($booked_amount)>0){
            foreach($booked_amount->toArray() as $records){
                $res = explode(',',str_replace("'", "", $records['amount_booked']));
                $total_amount_booked +=  array_sum($res);
            }
        }
        $total_amount = $total[0]->total_amount ?? 0;

        $remaining_amount = roundOff($total_amount - $total_amount_booked,4);

        return $remaining_amount;
    }

    public static function updateConstructionEditWagesCase($data)
    {
        if(!empty($data['apartment_id']) || !empty($data['floor_id'])){

            $wages_name = $data['pay_to'];
            $apartment_id = $data['apartment_id']??null;
            $floor_id = $data['floor_id']??null;
            $return = ConstructionDetails::select('amount_booked','name')->whereNull('deleted_at')->where('project_id',$data['project_id'])
            ->where('block_id',$data['block_id'])->where('main_description_id',$data['main_description_id'])
            ->where('sub_description_id',$data['sub_description_id'])
            ->whereRaw("name like '%$wages_name%'" );

            if(!empty($apartment_id)){
                $return = $return->where('apartment_id',$data['apartment_id'])->first();
            } else {
                $return = $return->where('floor_id',$data['floor_id'])->first();
            }
            

            if(!empty($return)){
                $amount = explode(",",$return['amount_booked']);
                $name = explode(",",$return['name']);
            
                $j = count($amount);
                for($i = 0; $i<count($amount); $i++){
        
                    $j--;
                    if(isset($amount[$j])){
                        if($amount[$j] == $data['old_amount'] && $name[$j] == $wages_name){
                            $amount[$j] = $data['sum'];
                        }
                    }
                
                }
                $updated_amount = implode(",",$amount);

                if(!empty($apartment_id)){
                    DB::select("UPDATE construction_details SET amount_booked = '$updated_amount' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$data['project_id']." and block_id = ".$data['block_id']." and main_description_id =".$data['main_description_id']." and apartment_id =".$data['apartment_id']." and sub_description_id =".$data['sub_description_id']." and name like '%$wages_name%' ) as cunst)");
                } else {
                    DB::select("UPDATE construction_details SET amount_booked = '$updated_amount' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$data['project_id']." and block_id = ".$data['block_id']." and main_description_id =".$data['main_description_id']." and floor_id =".$data['floor_id']." and sub_description_id =".$data['sub_description_id']." and name like '%$wages_name%' ) as cunst)");
                }
                
            }
        }else{
            throw new AppException("For edit wages apartment or floor is required.");
        }
    }

    public static function updateConstructionDeleteWagesCase($wage_id)
    {
        $data = WagesDetails::getWagesById($wage_id)->toArray();
        $wages_name = $data['pay_to'];
        $apartment_id = $data['apartment_id']??null;
        $floor_id = $data['floor_id']??null;
        $return = ConstructionDetails::select('amount_booked','name','wages')->whereNull('deleted_at')->where('project_id',$data['project_id'])
        ->where('block_id',$data['block_id'])->where('main_description_id',$data['main_description_id'])
        ->where('sub_description_id',$data['sub_description_id'])
        ->whereRaw("name like '%$wages_name%'" );

        if(!empty($apartment_id)){
            $return = $return->where('apartment_id',$data['apartment_id'])->first();
        }else{
            $return = $return->where('floor_id',$data['floor_id'])->first();
        }
        

        if(!empty($return)){
            $amount = explode(",",$return['amount_booked']);
            $name = explode(",",$return['name']);
            $wages_no = explode(",",$return['wages']);
           
            $j = count($amount);
            for($i = 0; $i<count($amount); $i++){
    
                $j--;
                if(isset($amount[$j]) && isset($name[$j])){
                    if($amount[$j] == $data['sum'] && $name[$j] == $wages_name){
                        unset($amount[$j]);
                        unset($name[$j]);
                        unset($wages_no[$j]);
                    }
                }
               
            }
            $updated_amount = implode(",",$amount);
            $updated_name = implode(",",$name);
            $updated_wages_no = implode(",",$wages_no);
            //print_r($updated_amount);pp($updated_name);
            if(!empty($apartment_id)){
                DB::select("UPDATE construction_details SET amount_booked = '$updated_amount',`name`= '$updated_name',`wages`= '$updated_wages_no' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$data['project_id']." and block_id = ".$data['block_id']." and main_description_id =".$data['main_description_id']." and apartment_id =".$data['apartment_id']." and sub_description_id=".$data['sub_description_id']." and name like '%$wages_name%' ) as cunst)");
            }else{
                DB::select("UPDATE construction_details SET amount_booked = '$updated_amount',`name`= '$updated_name' ,`wages`= '$updated_wages_no' WHERE id = ( SELECT * FROM(Select min(id) as id from construction_details where project_id = ".$data['project_id']." and block_id = ".$data['block_id']." and main_description_id =".$data['main_description_id']." and floor_id =".$data['floor_id']." and sub_description_id=".$data['sub_description_id']." and name like '%$wages_name%' ) as cunst)");
            }
            
        }
    }

    public static function remainingBalanceCheck($project_id,$block_id,$apartment_id,$floor_id,$main_description_id,$sub_description_id)
    {
        if(!empty($apartment_id)){
            $booked_amount = ConstructionDetails::select('total','amount_booked')->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$project_id)->where('construction_details.main_description_id',$main_description_id)
            ->where('construction_details.block_id',$block_id)->where('construction_details.apartment_id',$apartment_id)
            ->where('construction_details.sub_description_id',$sub_description_id)->get();
            $total_amount   =   $total_amount_booked = 0;
            if(count($booked_amount)>0){
                foreach($booked_amount->toArray() as $booked_amount_value){
                    $res = explode(',',str_replace("'", "", $booked_amount_value['amount_booked']));
                    $total_amount   += floatval(preg_replace('/[^\d.]/', '',$booked_amount_value['total']));
                    $total_amount_booked +=  array_sum($res);
                }
            }
        }else{
            $booked_amount = ConstructionDetails::select('total','amount_booked')->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$project_id)->where('construction_details.main_description_id',$main_description_id)
            ->where('construction_details.block_id',$block_id)->whereNull('construction_details.apartment_id')->where('construction_details.floor_id',$floor_id)
            ->where('construction_details.sub_description_id',$sub_description_id)->get();
            $total_amount   =   $total_amount_booked = 0;
            if(count($booked_amount)>0){
                foreach($booked_amount->toArray() as $booked_amount_value){
                    $res = explode(',',str_replace("'", "", $booked_amount_value['amount_booked']));
                    $total_amount   += floatval(preg_replace('/[^\d.]/', '',$booked_amount_value['total']));
                    $total_amount_booked    +=  array_sum($res);
                }
            }
        }
        $return = (($total_amount-$total_amount_booked)<0)?0:$total_amount-$total_amount_booked;
        return $return;
    }

    public static function remainingBalanceCheckMultipleCase($project_id,$block_id,$apartment_id,$floor_id,$main_description_id,$sub_description_id)
    {
        if(!empty($apartment_id)){
            $booked_amount = ConstructionDetails::select('total','amount_booked')->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$project_id)->where('construction_details.main_description_id',$main_description_id)->where('construction_details.sub_description_id',$sub_description_id)
            ->where('construction_details.block_id',$block_id)->where('construction_details.apartment_id',$apartment_id)->get();
            $total_amount   =   $total_amount_booked = 0;
            if(count($booked_amount)>0){
                foreach($booked_amount->toArray() as $booked_amount_value){
                    $res = explode(',',str_replace("'", "", $booked_amount_value['amount_booked']));
                    $total_amount   += floatval(preg_replace('/[^\d.]/', '',$booked_amount_value['total']));
                    $total_amount_booked +=  array_sum($res);
                }
            }
        }else{
            $booked_amount = ConstructionDetails::select('total','amount_booked')->whereNull('construction_details.deleted_at')
            ->where('construction_details.project_id',$project_id)->where('construction_details.main_description_id',$main_description_id)->where('construction_details.sub_description_id',$sub_description_id)
            ->where('construction_details.block_id',$block_id)->whereNull('construction_details.apartment_id')->where('construction_details.floor_id',$floor_id)->get();
            $total_amount   =   $total_amount_booked = 0;
            if(count($booked_amount)>0){
                foreach($booked_amount->toArray() as $booked_amount_value){
                    $res = explode(',',str_replace("'", "", $booked_amount_value['amount_booked']));
                    $total_amount   += floatval(preg_replace('/[^\d.]/', '',$booked_amount_value['total']));
                    $total_amount_booked    +=  array_sum($res);
                }
            }
        }
        $return = (($total_amount-$total_amount_booked)<0)?0:$total_amount-$total_amount_booked;
        return $return;
    }

    public static function getRates($project_id,$block_id,$apartment_id,$floor_id,$main_description_id,$sub_description_id)
    {
        $return = ConstructionDetails::select('lab_rate')->whereNull('deleted_at') ->where('project_id',$project_id)->where('main_description_id',$main_description_id)->where('sub_description_id',$sub_description_id)
        ->where('block_id',$block_id)->where('apartment_id',$apartment_id)->where('floor_id',$floor_id)->first();

        if(!empty($return['lab_rate'])){
            return $return['lab_rate'];
        }
        return '';
    }

    public static function getTotalOfProject($request)
    {
        $total = $booked = 0;

        $data = ConstructionDetails::select('total','amount_booked')->whereNull('deleted_at')->where('project_id', $request['project_id'])->get();

        if(count($data)>0){
            $data = $data->toArray();
        }else{
            $data = [];
        }

        foreach($data as $value){
            $total += (float)$value['total'];
            $res = explode(',',str_replace("'", "", $value['amount_booked']));
            $booked +=  array_sum($res);
        }

        $response['total_amount'] = round($total,3);
        $response['booked_amount'] = round($booked,3);

        return $response;
    }
}
