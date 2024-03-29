<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\AppException;
use App\Exceptions\BusinessExceptions\RegisterFailedException;
use App\Models\UserAuthorization;
use App\Models\UserProjectLinking;
use Illuminate\Console\Application;
use Illuminate\Support\Facades\File;

class UserServices{

    public function login($request){

        $user = User::getUserByEmail($request['email']);

        if (!$user) {
            //event(new EventLog($user="",Event::LOGIN_FAILED_ATTEMPT,Event::LOGIN_EVENT));
            throw new AppException('Your Account does not exists.');
        } elseif(!Auth::validate(['email'=>$request['email'],'password'=>$request['password']])) {
            throw new AppException("Invalid Credentials");
        }
        //OtpVerificationServices::emailVerificationRequest($request);

        $user['token'] = $user->createToken('MyApp')->accessToken;
        
        $data = [
            'user_id' => $user['id'],
            'token' => $user['token']
        ];

        UserAuthorization::addToken($data);

        return $user;

    }

    public function register($request){

        $input = [
            "first_name"=>$request['first_name'],
            "last_name"=>$request['last_name'],
            "email"=>$request['email'],
            'user_role' => $request['user_role'],
            "password"=>bcrypt($request['password']),
        ];

        if(User::getUserByEmail($request['email'])){
            throw new AppException("Email Address is already registerd with us. PLease login.");
        }
        else{
            // try {
                User::register_user($input);
            // }
            // catch(\Exception $e){
            //     return $e->getMessage();
            // }
        }

    }

    public static function cleanServerDirectory()
    {
        $construction_count = 0;
        $wages_count = 0;
        $storage_count = 0;
        $str = 0;
        $path = public_path('construction_data/');
        if(file_exists($path)){
            $files = scandir(public_path('construction_data/'));
            //$files =  File::allFiles($path);
            foreach($files as $value){
                if ($value != "." && $value != "..") {
                    if (file_exists($path.$value))
                    $flag = unlink($path.$value);$construction_count++;
                }
            }
            echo "Public Construction data files cleaned ".$construction_count."\n";
        }

        $path = public_path('storage/');
        if(file_exists($path)){
            $files = scandir(public_path('storage/'));
            //$files =  File::allFiles($path);
            foreach($files as $value){
                if ($value != "." && $value != "..") {
                    if (file_exists($path.$value))
                    $flag = unlink($path.$value);$storage_count++;
                }
            }
            echo "Public Storage files cleaned ".$storage_count."\n";
        }

        $path = public_path('wages_data/');
        if(file_exists($path)){
            $files = scandir(public_path('wages_data/'));
            //$files =  File::allFiles($path);
            foreach($files as $value){
                if ($value != "." && $value != "..") {
                    if (file_exists($path.$value))
                    $flag = unlink($path.$value);$wages_count++;
                }
            }
            echo "Public Wages data files cleaned ".$wages_count."\n";
        }
        
        $path = $_SERVER['DOCUMENT_ROOT']."/storage";
        
         if(file_exists($path)){
             $files = scandir($path);
             //$files =  File::allFiles($path);
             foreach($files as $value){
                 if ($value != "." && $value != "..") {
                     if (file_exists($path.$value))
                    $flag = unlink($path);$str++;
                 }
            }
            echo "Wages Portal Storage files cleaned => ".$str."\n";
        }

    }

    public static function wagesPortalController($request)
    {
        $path = $_SERVER['DOCUMENT_ROOT']."/".$request['folder'];
        
         if(file_exists($path)){
            //  $files = scandir($path);pp($files);
            //  //$files =  File::allFiles($path);
            //  foreach($files as $value){
            //      if ($value != "." && $value != "..") {
            //          if (file_exists($path.$value))
                    $flag = rmdir($path);
            //      }
            // }
            echo "Wages Portal files cleaned => ".$path."\n";
        }
    }

    public static function getUserList()
    {
        return User::getUserList();
    }

    public static function updateUserRole($request)
    {
        if($request['role_request'] == 'delete')
        {
            User::deleteUser($request['id']);
        }else{
            User::updateUserRole($request['id'],$request['role_request']);
        }
    }

    public static function forgotPassword($request)
    {
        $user = User::getUserByEmail($request['email']);

        if (!$user) {
            throw new AppException('Your Account does not exists.');
        } 

        $input = [
            "password"=>bcrypt($request['password']),
        ];

        User::updatePassword( $user['id'], $input);
    }

    public static function decryptPassword($request)
    {
        $user = User::getUserByEmail($request['email']);

        if (!$user) {
            throw new AppException('Your Account does not exists.');
        }

        $password['decrypted_password'] = decrypt($user['password']);

        return $password;
    }
    public static function getUserProjectLinkingDetails($request)
    {
        $user_list = User::getUserListForLinking($request);
        $total_count = User::getUserCount();

        foreach($user_list as &$value){
            $project_details = UserProjectLinking::getUserProjectDetails($value);

            $value['project_details'] = $project_details;
        }
        $return['total_records'] = $total_count;
        $return['user_list'] = $user_list;

        return $return;
        
    }

    public static function linkUserAndProjects($request)
    {
        if($request['operation'] == 'remove'){
            if(UserProjectLinking::deleteLinkedUser($request['user_id'],$request['project_id']))
            {
                return;
            }
            throw new AppException("No operation performed.");
        }

        if($request['operation'] == 'add')
        {
            if(UserProjectLinking::linkUserAndProjects(['user_id' => $request['user_id'],'project_id' => $request['project_id']]))
            {
                return;
            }
            throw new AppException("No operation performed.");
        }
        throw new AppException("No operation performed.");

    }

}