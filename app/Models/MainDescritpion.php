<?php

namespace App\Models;

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
}
