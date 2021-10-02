<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetApartmentFormRequest;
use App\Http\Requests\GetBlockDetailsFormRequest;
use App\Http\Requests\GetConstructionDetailsFormRequest;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Services\ConstructionDetailsServices;


class ConstructionDetailsController extends Controller
{

    public static function getProjectDetails(GetProjectDetialsFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::getProjectDetails($request);

        return  response()->data($data);
    }

    public static function getBlockDetails(GetBlockDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::getBlockDetails($request);

        return  response()->data($data);
    }

    public static function getApartmentDetails(GetApartmentFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::getApartmentDetails($request);

        return  response()->data($data);
    }

    public static function getConstructionDetails(GetConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::getConstructionDetails($request);

        return  response()->data($data);
    }

    public static function getDescriptionWork(GetApartmentFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::getDescriptionWork($request);

        return  response()->data($data);
    }
    
}
