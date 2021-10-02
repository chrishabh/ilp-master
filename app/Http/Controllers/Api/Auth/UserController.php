<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Services\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpFormRequest;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class UserController extends Controller
{

    public static function userSignUp(SignUpFormRequest $request)
    {
         $requestData = $request->validated();

            $user = new UserServices();
            $user->register($request);

		return  response()->data([],'Registration Success');
    }

    public static function userLogin(LoginFormRequest  $request)
    {

        $requestData = $request->validated();
        $user = new UserServices();
        $data = $user->login($request);
        return  response()->data(['user'=>$data]);

    }

    public static function test()
    {

        
        return  response()->data(['user'=>"data",'client' => "rishabh"]);

    }
    
}
