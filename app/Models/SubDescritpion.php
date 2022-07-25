<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubDescritpion extends Model
{
    use HasFactory;

    public static function getSubDescriptionId($sub_description)
    {
        $return = SubDescritpion::whereNull('deleted_at')->where('sub_description',$sub_description)->first();

        if(isset($return->id)){
            return $return->id;
           
        } else {
            throw new  AppException("Sub Description does not exists in system i.e '".$sub_description."'");
        }
    }

    public static function checckSubDescription($sub_description_array = [])
    {

        foreach($sub_description_array as $value){
            $return = SubDescritpion::whereNull('deleted_at')->where('sub_description',$value)->exists();
            if(!$return){
                throw new  AppException("Sub Description does not exists in the system i.e ".$value);
            }
        }
        return ;
    }

    public static function insertSubDescription($sub_description_array = [])
    {
        $inserted_data = [];
        foreach($sub_description_array as $value){
            $return = SubDescritpion::whereNull('deleted_at')->where('sub_description',ltrim(trim($value," ")))->exists();
            if(!$return){
                $insert['sub_description'] = ltrim(trim($value," "));
                $insert['main_description_id'] = '1';
                $insert['apartment_id'] = '0';
                $insert['block_id'] = '0';
                $insert['project_id'] = '0';
                DB::table('sub_descritpions')->insert($insert);
                $inserted_data  [] = $insert;
            }
        }
        return $inserted_data ;
    }
}
