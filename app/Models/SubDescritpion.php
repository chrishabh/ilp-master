<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDescritpion extends Model
{
    use HasFactory;

    public static function getSubDescriptionId($sub_description)
    {
        $return = SubDescritpion::whereNull('deleted_at')->where('sub_description',$sub_description)->first();

        if(isset($return->id)){
            return $return->id;
           
        } else {
            throw new  AppException("Sub Description does not exists in system");
        }
    }
}
