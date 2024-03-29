<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\AppException;
use App\Exceptions\BusinessExceptions\RegisterFailedException;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Jobs\ImportExcel;
use App\Models\ApartmentDetails;
use App\Models\BlockDetails;
use App\Models\ConstructionDetails;
use App\Models\Floor;
use App\Models\ImportExcelTable;
use App\Models\MainDescritpion;
use App\Models\PayToDetails;
use App\Models\ProjectDetails;
use App\Models\UserAuthorization;
use Carbon\Carbon;
use Illuminate\Console\Application;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\Unset_;

class ConstructionDetailsServices{

    public static function getProjectDetails($request)
    {
        $return['total_records'] = ProjectDetails::getProjectTotalRecords($request);
        $return['project_details'] = ProjectDetails::getProjectDetails($request);

        return  $return;
    }

    public static function getProject()
    {
       
        $return['project_details'] = ProjectDetails::getProject();

        return  $return;
    }

    public static function getBlockDetails($request)
    {
        $return['total_records'] = BlockDetails::getBlockTotalRecords($request);
        $return['block_details'] = BlockDetails::getBlockDetails($request);

        return  $return;
    }

    public static function getApartmentDetails($request)
    {
        $return['total_records'] = ApartmentDetails::getApartmentTotalRecords($request);
        $return['apartment_details'] = ApartmentDetails::getApartmentDetails($request);

        return  $return;
    }

    public static function getFloorDetails($request)
    {
        $return['total_records'] = Floor::getFloorTotalRecords($request);
        $return['floor_details'] = Floor::getFloorDetails($request);

        return  $return;
    }

    public static function getConstructionDetails($request)
    {
        if(isset($request['apartment_id']) || isset($request['floor_id'])){
            return ConstructionDetails::getConstructionDetails($request);
        } else {
            throw new AppException("For construction details apartment or floor is required.");
        }
    }

    public static function getDescriptionWork($request)
    {
        if(isset($request['apartment_id']) || isset($request['floor_id'])){
            return ConstructionDetails::getDescriptionWork($request);
        } else {
            throw new AppException("For construction details apartment or floor is required.");
        }
    }

    public static function addConstructionDetails($request)
    {
        $return = ConstructionDetails::addConstructionDetails($request);

        if($return){
            return $request->toArray();
        }else{
            ['message'=> 'Somethig went wrong'];
        }
    }

    public static function updateConstructionDetails($request)
    {
        $return = ConstructionDetails::updateConstructionDetails($request['id'],$request->toArray());

        if($return){
            return true;
        }else{
            return false;
        }
    }

    public static function addProjectDetails($request)
    {
        return ProjectDetails::addProjectDetails($request->toArray());
    }

    public static function addBlockDetails($request)
    {
        return BlockDetails::addBlockDetails($request->toArray());
    }

    public static function addApartmentDetails($request)
    {
        return ApartmentDetails::addApartmentDetails($request->toArray());
    }

    public static function uploadExcelForData($request)
    {
        if (isset($_FILES) && !empty($_FILES['request']['name']['file'])) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 180);
            $dir_name =  $_SERVER['DOCUMENT_ROOT']."/storage"."//";
            //$dir_name =  env('VIDEOS_PATH')."/storage"."//";
            if (!is_dir($dir_name)) {
                @mkdir($dir_name, "0777", true);
            }

            $current_timestamp  = Carbon::now()->timestamp;
            $video_saved_name = $current_timestamp . $_FILES['request']['name']['file'];
            

            $video_data['video_name'] =  $_FILES['request']['name']['file'];
            $video_data['video_path'] = $dir_name.$video_saved_name;
            $request->file->move($dir_name, $video_saved_name);
            $data = [
                'file_path' => $video_data['video_path'],
                'cron_timing' => '',
                'progress' => '0'
            ];
            ImportExcelTable::insertFilePath($data);
            return ['file_path' => $video_data['video_path']];
            ImportExcel::dispatch($video_data['video_path'])->onQueue('high');
            //importExcelToDB($video_data['video_path']);
        }
    }

    public static function importExcelFile($request){

        if (isset($_FILES) && !empty($_FILES['request']['name']['file'])) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 180);
            $dir_name =  $_SERVER['DOCUMENT_ROOT']."/storage/description_files"."//";
            //$dir_name =  env('VIDEOS_PATH')."/storage"."//";
            if (!is_dir($dir_name)) {
                @mkdir($dir_name, "0777", true);
            }

            $current_timestamp  = Carbon::now()->timestamp;
            $video_saved_name = $current_timestamp . $_FILES['request']['name']['file'];
            

            $video_data['video_name'] =  $_FILES['request']['name']['file'];
            $video_data['video_path'] = $dir_name.$video_saved_name;
            $request->file->move($dir_name, $video_saved_name);
            // $data = [
            //     'file_path' => $video_data['video_path'],
            //     'cron_timing' => '',
            //     'progress' => '0'
            // ];
            // ImportExcelTable::insertFilePath($data);
            // return ['file_path' => $video_data['video_path']];
            // //importExcelToDB($video_data['video_path']);
            if(!empty($video_data['video_path'])){
                return MainDescritpion::importMainDesc($video_data['video_path']);
            }
        }
    }

    public static function getPayToDetails($request)
    {
        $return['total_records'] = PayToDetails::getPayToTotalRecords();
        $return['pay_to_details'] = PayToDetails::getPayToDetails($request);

        return  $return;
    }

    public static function getProjectExcelForConstructionDetails($request)
    {
        if(isset($request['part'])){
            $block_id = BlockDetails::getBlockIdForPart($request['project_id'],$request['part']);
            $details = ConstructionDetails::getConstructionDetailsForProject($request['project_id'],$block_id);
        }else{
            $details = ConstructionDetails::getConstructionDetailsForProject($request['project_id']);
        }

        $aparment_aaray = [];
        $floor_array = [];
        $final_data = [];
        $project_name = null;
        foreach($details as &$value)
        {
            try{
                if(($value['Total']>0 || $value['Total']<0) && ($value['Rate']>0 || $value['Rate']<0)){
                    $value['Area'] = ($value['Rate'] != '0')?roundOff((float)$value['Total']/(float)$value['Rate'],1):'';
                }else{
                    $value['Area'] = '';
                }
            }catch(\Exception $e){
                throw new AppException('Something went wrong on Block =>'.$value['Block'].',Level=>'.$value['Level'].',Plot=>'.$value['Plot'].',Main Description=>'.$value['Main Description'].',Sub Description=>'.$value['Sub Description']);
            }
            $array_value = [];
            if(!empty($value['Total'])){
                $value['Total'] = "£".roundOff($value['Total']);
            }
            $amount = explode(",",$value['Amount']);
            $booked_amount = 0;
            foreach($amount as $amount_value)
            {
                if(!empty($amount_value)){
                    $amount_value = str_replace("'","",$amount_value);
                    $array_value [] = roundOff($amount_value);
                    $booked_amount += roundOff($amount_value);
                }

            }
            $quantity = ($value['Rate'] > '0' && $booked_amount > 0)?roundOff((float)$booked_amount/(float)$value['Rate'],1):'';
            $value['Amount'] = implode(",",$array_value);
            $value['Qty.'] = (number_format((float)$quantity,2)>0)?number_format((float)$quantity,2):'';
            $project_name = $value['project_name'];
            unset($value['project_name']);
            // if(!empty($value['Apartment'])){
            //     $aparment_aaray [] = $value;
            // }else{
            //     $floor_array [] = $value;
            // }
        }
        // $aparment_aaray = group_by('Apartment',$aparment_aaray);
        // $floor_array = group_by('Floor',$floor_array);
        // $final_data = array_merge($floor_array,$aparment_aaray);
        $return['download_url'] = downloadConstructionExcelFile($details,"Project_details",$project_name);
        return $return;
       
    }

    public static function deleteProject($request)
    {
        ProjectDetails::deleteProject($request['project_id']);
        BlockDetails::deleteBlockByProject($request['project_id']);
        ApartmentDetails::deleteApartmentByProject($request['project_id']);
        Floor::deleteFloorByProject($request['project_id']);
        ConstructionDetails::deleteProjectConstructionDetails($request['project_id']);
    }

    public static function editConstructionDetails($request)
    {
       $data = [
        'project_id' => $request['project_id'],
        'block_id' => $request['block_id'],
        'apartment_id' => $request['apartment_id'],
        'main_description_id' => $request['main_description_id'],
        'sub_description_id' => $request['sub_description_id'],
        'description' =>  $request['description'],
        'area' => $request['area'],
        'lab_rate' => $request['lab_rate'],
        'unit' =>  $request['unit'],
       ];

       ConstructionDetails::updateConstructionDetails($request['id'],$data);
    }

    public static function getTotalOfProject($request)
    {
        return ConstructionDetails::getTotalOfProject($request);
    }

}