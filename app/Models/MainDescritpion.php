<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
