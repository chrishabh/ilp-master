<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\LoginFormRequest;
use App\Services\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpFormRequest;
use App\Services\VideosServices;
use Illuminate\Http\Request;

class VideosController extends Controller
{

    public static function uploadVideo(Request $request)
    {
        //  $requestData = $request->validated();

        $return = VideosServices::uploadVideos($request);

		return  response()->data($return);
    }

    public static function downloadVideo(Request $request)
    {

        $return = VideosServices::downloadVideos($request);

		return  response()->data($return);

    }
    
}
