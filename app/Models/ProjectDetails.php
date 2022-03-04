<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public static function getProjectDetails($request)
    {
        $noOfRecord = $request['no_of_records'] ?? 10;
        $current_page = $request['page_number'] ?? 1;
        $offset = ($current_page*$noOfRecord)-$noOfRecord;

        $data = ProjectDetails::whereNull('deleted_at')->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getProjectTotalRecords()
    {
        return ProjectDetails::whereNull('deleted_at')->count('id');
    }

    public static function addProjectDetails($data){

        return ProjectDetails::insertGetId($data);
    }

    public static function getProjectId($Project_name)
    {
        $return = ProjectDetails::whereNull('deleted_at')->where('project_name',$Project_name)->first();

        if(isset($return->excel_imported)){
            if($return->excel_imported){
                //throw new AppException('This Project already exists');
            }
        }

        if(isset($return->id)){
            return $return->id;
           
        } else {
            return ProjectDetails::insertGetId(['project_name'=>$Project_name]);
        }
    }

    public static function updatedImportedFlag($Project_id)
    {
        return ProjectDetails::where('id',$Project_id)->update(['excel_imported'=>'1']);
    }

    public static function deleteProject($Project_id)
    {
        return ProjectDetails::where('id',$Project_id)->update(['deleted_at'=>date('Y-m-d')]);
    }
}
