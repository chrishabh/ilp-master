<?php

namespace App\Models;

use App\Exceptions\AppException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProjectLinking extends Model
{
    use HasFactory;

    public static function getUserProjectDetails($data)
    {
        $data = UserProjectLinking::select('project_details.project_name','user_project_linkings.project_id')
        ->Join('project_details','project_details.id','=','user_project_linkings.project_id')->whereNull('project_details.deleted_at')
        ->whereNull('user_project_linkings.deleted_at')->where('user_id',$data['id'])->get();

        if(count($data)>0){
            return $data->toArray();
        }

        return [];
    }

    public static function deleteLinkedUser($user_id,$project_id)
    {
        return UserProjectLinking::whereNull('deleted_at')->where('user_id',$user_id)->where('project_id',$project_id)->update(['deleted_at'=> Carbon::now()]);
    }

    public static function linkUserAndProjects($data = [])
    {
        if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->exists())){
            return UserProjectLinking::insert($data);
        }else{
            throw new AppException('Project already linked please select another project');
        }
        
    }
}
