<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\AppException;
use App\Exceptions\BusinessExceptions\RegisterFailedException;
use Illuminate\Console\Application;

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

        return $user;

    }

    public function register($request){

        $input = [
            "first_name"=>$request['first_name'],
            "last_name"=>$request['last_name'],
            "email"=>$request['email'],
            #"phone_number"=>$request->phone_number,
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

}