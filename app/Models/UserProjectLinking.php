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

    public static function deleteFloorLinkedUser($user_id,$project_id,$floor_id)
    {
        return UserProjectLinking::whereNull('deleted_at')->where('user_id',$user_id)->where('project_id',$project_id)->where('floor_id',$floor_id)->update(['deleted_at'=> Carbon::now()]);
    }

    public static function deleteMainAndSubDescription($user_id,$project_id,$floor_id,$main_description,$sub_description)
    {
        return UserProjectLinking::whereNull('deleted_at')->where('user_id',$user_id)->where('project_id',$project_id)->where('floor_id',$floor_id)->whereIn('main_description_id',$main_description)->whereIn('sub_description_id',$sub_description)->update(['deleted_at'=> Carbon::now()]);
    }

    public static function linkUserAndProjects($data = [])
    {
        if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->exists())){
            return UserProjectLinking::insert($data);
        }else{
            throw new AppException('Project already linked please select another project');
        }
        
    }

    public static function linkUserAndFloors($data = [])
    {
        if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->exists())){
           if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->whereNull('floor_id')->exists()))
            return UserProjectLinking::insert($data);
            return UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->update(['floor_id'=> $data['floor_id']]);

        }else{
            throw new AppException('Floor already linked please select another project');
        }
        
    }

    public static function linkMainDescription($data = [])
    {
        $id = null;
        if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->where('main_description_id',$data['main_description_id'])->exists())){
           if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->whereNull('main_description_id')->exists())){
            return UserProjectLinking::insert($data);

           }else{
            $id = UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_(id'])->where('floor_id',$data['floor_id'])->whereNull('sub_description_id')->first()->id;
            return UserProjectLinking::whereNull('deleted_at')->where('id',$id)->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->update(['main_description_id'=> $data['main_description_id']]);

           }

        }else{
            throw new AppException('Main Description already linked please select another project');
        }
        
    }

    public static function linkSubDescription($data = [])
    {
        $id = null;
        if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->where('sub_description_id',$data['sub_description_id'])->exists())){
            if(!(UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->whereNull('sub_description_id')->exists())){
                return UserProjectLinking::insert($data);

            }else{
                $id = UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->whereNull('sub_description_id')->first()->id;
                return UserProjectLinking::whereNull('deleted_at')->where('user_id',$data['user_id'])->where('id',$id)->where('project_id',$data['project_id'])->where('floor_id',$data['floor_id'])->update(['sub_description_id'=> $data['sub_description_id']]);

            }
 
         }else{
             throw new AppException('Sub Description already linked please select another project');
         }
        
    }
}
