<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Services\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordFormRequest;
use App\Http\Requests\GetPayToDetailsFormRequest;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Http\Requests\LinkUserAndProjectsFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Http\Requests\UpdateUserRoleFormRequest;
use App\Models\LookUpValue;
use Illuminate\Http\Request;
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

    public static function lookUpValue()
    {
        $data = LookUpValue::getLookUpValue();
        return  response()->data(['look_up'=>$data]);
    }

    public static function test()
    {
        UserServices::cleanServerDirectory();
    }

    public static function wagesPortalController(Request $request)
    {
        UserServices::wagesPortalController($request);
    }


    public static function getUserList()
    {
        $user = new UserServices();
        $data = $user->getUserList();
        return  response()->data(['user_list'=>$data]);
    }

    public static function updateUser(UpdateUserRoleFormRequest  $request)
    {
        $user = new UserServices();
        $data = $user->updateUserRole($request);
        return  response()->success();
    }

    public static function forgotPassword(ForgotPasswordFormRequest $request)
    {
        $user = new UserServices();
        $data = $user->forgotPassword($request);
        return  response()->success();
    }

    public static function decryptPassword(ForgotPasswordFormRequest $request)
    {
        $user = new UserServices();
        $data = $user->decryptPassword($request);
        return  response()->data($data);
    }
    public static function getUserProjectLinkingDetails(GetPayToDetailsFormRequest $request)
    {
        $requestData    =   $request->validated();
        $user           =   new UserServices();
        $data           = $user->getUserProjectLinkingDetails($request);

        return  response()->data($data);
    }

    public static function linkUserAndProjects(LinkUserAndProjectsFormRequest $request)
    {
        $requestData    =   $request->validated();
        $user           =   new UserServices();
        $data           = $user->linkUserAndProjects($request);

        return  response()->success();
    }
    
}
