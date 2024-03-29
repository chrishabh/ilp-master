<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LookUpValue extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'id',
        'deleted_at',
    ];

    public static function getLookUpValue(){
        $return = LookUpValue::whereNull('deleted_at')->get();
        if(count($return)>0){
            return $return->toArray();
        }
        return [];
    }

    public static function addWagesNumber($request)
    {
        LookUpValue::whereNull('deleted_at')->where('module','wages_number')->where('type','wage-number')->where('key','number')->update(['text'=>$request['wages_number']]);
    }
}
