<?php

namespace App\Models;

use App\Exceptions\AppException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuthorization extends Model
{
    use HasFactory;

    public static function addToken($data){

        return UserAuthorization::insert($data);
    }

    public static function verifyToken($token)
    {
        $token=explode(' ',$token);
        $return = UserAuthorization::whereNull('deleted_at')->where('token',$token[1])->first();
       
        if(!empty($return)){ 
            if(Carbon::parse($return->created_at)->addMinutes(env('PERSONAL_ACCESS_TOKEN_EXPIRY_SECONDS',30))->isPast()){pp($return);
                throw new AppException('Unauthorised Token',null,401,401);
            }
            return 1;
        }else{
            throw new AppException('Unauthorised Token',null,401,401);
        }
        
    }
}
