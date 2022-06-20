<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function register_user($input)
    {
        $id = DB::table('users')->insertGetId($input);
        return $id;
    }

    public static function getUserByEmail($email)
    {
        return User::whereNull('deleted_at')->where('email', $email)->first();
    }

    public static function getUserById($user_id)
    {
        return User::where('id', $user_id)->first();
    }

    public static function details(){
        $headerStringValue = apache_request_headers();
        if(!empty($headerStringValue['Authorization'])){
            $token = explode(' ',$headerStringValue['Authorization']);
            return User::join('user_authorizations','user_authorizations.user_id','=','users.id')->whereNull('users.deleted_at')->where('user_authorizations.token',$token[1])->first();
            
        }
        return (object)[];
    }

    public static function getUserList()
    {
        $return = User::whereNull('deleted_at')->get();

        if(count($return)>0){
            return $return->toArray();
        }

        return [];
    }

    public static function deleteUser($id)
    {
        User::where('id',$id)->update(['deleted_at' =>date('Y-m-d')]);
    }

    public static function updateUserRole($id,$role)
    {
        User::whereNull('deleted_at')->where('id',$id)->update(['user_role' =>$role]);
    }
}
