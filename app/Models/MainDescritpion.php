<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MainDescritpion extends Model
{
    use HasFactory;

    public static function getDistinctDescription(){
        $return = MainDescritpion::select('description as description_header')->whereNull('deleted_at')->distinct()->get();
        if(count($return)>0){
            return $return->toArray();
        }
        return [];
    }

    public static function getMainDescriptionId($header_name)
    {
        $return =  MainDescritpion::whereNull('deleted_at')->where('description',$header_name)->first();

        if(isset($return->id)){
            return $return->id;
           
        } else {
            throw new  AppException("Main Description does not exists in system '".$header_name."'");
        }
    }

    public static function checkMainDescription($main_desc_array = [])
    {
        foreach($main_desc_array as $value){
            $return =  MainDescritpion::whereNull('deleted_at')->where('description',$value)->exists();
            if(!$return){
                throw new  AppException("Main Description does not exists in the system i.e ".$value);
            }
        }
        return ;
        
    }

    public static function importMainDesc($file_path)
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
                        if($columnA == "Main Description" && $columnB == "Sub Description"){
                            $is_verified = true;
                            continue;
                        }
                        if($is_verified){
                            if(!empty($columnA)){
                                $main_desc [] = $columnA;
                            }
                           if(!empty($columnB)){
                                $sub_desc [] = $columnB;
                           }
                           
                        }
                    }
                    $main_desc = array_unique($main_desc);
                    $data ['main_desc'] = [];
                    foreach($main_desc as $value){
                        $return =  MainDescritpion::whereNull('deleted_at')->where('description',ltrim(trim($value," ")))->exists();
                        if(!$return){
                           $insert['description'] = ltrim(trim($value," "));
                           $insert['apartment_id'] = '0';
                           $insert['block_id'] = '0';
                           $insert['project_id'] = '0';
                           DB::table('main_descritpions')->insert($insert);
                           $data ['main_desc'] [] = $insert;
                        }
                    }
                    $sub_desc = array_unique($sub_desc);
                    $data ['sub_desc'] = SubDescritpion::insertSubDescription($sub_desc);
                
                return $data;
            }
        }
       
    }
}
