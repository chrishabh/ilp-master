<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookWagesFormRequest;
use App\Http\Requests\GetBlockDetailsFormRequest;
use App\Http\Requests\GetWagesFormRequest;
use App\Services\ConstructionDetailsServices;
use App\Services\WagesServices;

class WagesBookingController extends Controller
{

    public static function bookWages(BookWagesFormRequest $request)
    {
        $requestData = $request->validated();
        WagesServices::bookWages($request);

        return  response()->success();
    }

    public static function getWages(GetWagesFormRequest $request)
    {
        $requestData = $request->validated();
        $data =  WagesServices::getWages($request);

        return  response()->data($data);
    }

    public static function getWagesExcel(GetWagesFormRequest $request)
    {
        $requestData = $request->validated();
        $data =  WagesServices::getWagesExcel($request);

        return  response()->data($data);
    }
}
