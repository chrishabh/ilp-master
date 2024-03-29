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

        $data = ProjectDetails::select('project_details.id','project_details.project_name')
        ->join('user_project_linkings','user_project_linkings.project_id','=','project_details.id')
        ->whereNull('project_details.deleted_at')
        ->whereNull('user_project_linkings.deleted_at')
        ->where('user_project_linkings.user_id',$request['user_id'])->offset($offset)->limit($noOfRecord)->get();

        if(count($data)>0){
            return $data->toArray();
        }
        return [];
    }

    public static function getProject()
    {

        $data = ProjectDetails::whereNull('deleted_at')->get();

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
            if($return->excel_imported && env('RESTRICT_DUPLICATE_PROJECT')){
                throw new AppException('This Project already exists.');
            }
        }
        $project_check = false;
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
