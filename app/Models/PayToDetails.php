<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PayToDetails extends Model
{
    use HasFactory;

    public static function getPayToDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = PayToDetails::whereNull('deleted_at')->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getPayToTotalRecords()
    {
        return PayToDetails::whereNull('deleted_at')->count('id');
    }

    public static function addPayToDetails($data)
    {
        return PayToDetails::insertGetId($data->toArray());
    }

    public static function getPayToCode($pay_name)
    {
        return PayToDetails::whereNull('deleted_at')->where('pay_to_name',$pay_name)->first();
    }

    public static function deletePayToDetails($request)
    {
        return PayToDetails::whereNull('deleted_at')->where('id',$request['id'])->update(['deleted_at' => date('Y-m-d')]);
    }

    public static function importPayToDetails($file_path)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);
        if(!empty($file_path)){
            $excel_data = [];
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file_path);
            $sheet_count = $spreadsheet->getSheetCount();
            $insert_data = [];

            for($i=0; $i<$sheet_count; $i++){

                 $highestRow = $spreadsheet->getSheet($i)->getHighestRow();
                    //$checkSheetData = $spreadsheet->getCell('')->toArray();
                    $is_verified = false;
                    for($k = 1; $k<=$highestRow; $k++){
                        $columnA =  $spreadsheet->getSheet($i)->getCellByColumnAndRow(1, $k)->getValue();
                        $columnB =  $spreadsheet->getSheet($i)->getCellByColumnAndRow(2, $k)->getValue();
                        if($columnA == "Pay To" && $columnB == "Pay To Code"){
                            $is_verified = true;
                            continue;
                        }
                        if($is_verified){
                            if(!empty($columnA)){
                                $pay_to_details ['pay_to_name'] = $columnA;
                                $pay_to_details ['pay_to_code'] = $columnB;
                            }
                        }

                        $data [] = $pay_to_details;
                    }
                    PayToDetails::truncate();
                    DB::table('pay_to_details')->insert($data);
                return $data;
            }
        }
       
    }
}
