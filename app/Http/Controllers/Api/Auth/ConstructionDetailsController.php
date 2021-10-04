<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddConstructionDetailsFormRequest;
use App\Http\Requests\AddProjectDetailsFormRequest;
use App\Http\Requests\GetApartmentFormRequest;
use App\Http\Requests\GetBlockDetailsFormRequest;
use App\Http\Requests\GetConstructionDetailsFormRequest;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Http\Requests\UpdateConstructionDetailsFormRequest;
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

    public static function getDescriptionWork(GetConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::getDescriptionWork($request);

        return  response()->data($data);
    }

    public function addConstructionDetails(AddConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $data = ConstructionDetailsServices::addConstructionDetails($request);

        return  response()->data($data);
    }

    public function updateConstructionDetails(UpdateConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $return = ConstructionDetailsServices::updateConstructionDetails($request);

        if($return){
            return  response()->success("Updated Successfully.");
        } else {
            throw new AppException('Something went wrong while updating construction details.');
        }
    }

    public function addProjectDetails(AddProjectDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $return = ConstructionDetailsServices::addProjectDetails($request);

        return  response()->data(['project_id'=>$return]);
    }

    public function addBlockDetails(AddProjectDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $return = ConstructionDetailsServices::addBlockDetails($request);

        return  response()->data(['block_id'=>$return]);
    }

    public function addApartmentDetails(AddProjectDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $return = ConstructionDetailsServices::addApartmentDetails($request);

        return  response()->data(['apartment_id'=>$return]);
    }
}
