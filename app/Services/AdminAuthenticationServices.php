<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Exceptions\AppException;
use App\Exceptions\BusinessExceptions\RegisterFailedException;
use App\Model\Admin;
use Illuminate\Console\Application;

class AdminAuthenticationServices
{

    public static function adminLogin($request){

        $user = Admin::getUser($request->email);

        if (!$user) {
            throw new AppException('Your Account does not exists.');
        } elseif($user['email'] != $request->email || $user['password'] != $request->password) {
            throw new AppException("Invalid Credentials");
        }

        $user['token'] = $user->createToken('Admin')->accessToken;

        return $user;

    }

    public static function getProductList($request){

        $return['product'] = 'tea';
        $return['cost'] = '350';

        return $return;
    }


}