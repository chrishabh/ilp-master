<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\AppException;
use App\Exceptions\BusinessExceptions\RegisterFailedException;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Models\ApartmentDetails;
use App\Models\BlockDetails;
use App\Models\ConstructionDetails;
use App\Models\ProjectDetails;
use Illuminate\Console\Application;

class ConstructionDetailsServices{

    public static function getProjectDetails($request)
    {

        $return['total_records'] = ProjectDetails::getProjectTotalRecords($request);
        $return['project_details'] = ProjectDetails::getProjectDetails($request);

        return  $return;
    }

    public static function getBlockDetails($request)
    {
        $return['total_records'] = BlockDetails::getBlockTotalRecords($request);
        $return['block_details'] = BlockDetails::getBlockDetails($request);

        return  $return;
    }

    public static function getApartmentDetails($request)
    {
        $return['total_records'] = ApartmentDetails::getApartmentTotalRecords($request);
        $return['apartment_details'] = ApartmentDetails::getApartmentDetails($request);

        return  $return;
    }

    public static function getConstructionDetails($request)
    {
        return ConstructionDetails::getConstructionDetails($request);
    }

    public static function getDescriptionWork($request)
    {
        return ConstructionDetails::getDescriptionWork($request);
    }

}