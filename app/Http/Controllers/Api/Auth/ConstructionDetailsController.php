<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddConstructionDetailsFormRequest;
use App\Http\Requests\AddProjectDetailsFormRequest;
use App\Http\Requests\EditConstructionDetailsFormRequest;
use App\Http\Requests\GetApartmentFormRequest;
use App\Http\Requests\GetBlockDetailsFormRequest;
use App\Http\Requests\GetConstructionDetailsFormRequest;
use App\Http\Requests\GetProjectConstructionDetailsFormRequest;
use App\Http\Requests\GetProjectDetialsFormRequest;
use App\Http\Requests\ImportExcelFileJobFormRequest;
use App\Http\Requests\UpdateConstructionDetailsFormRequest;
use App\Jobs\ImportExcel;
use App\Models\ImportExcelTable;
use App\Services\ConstructionDetailsServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function uploadExelForConstructionDetails(Request $request)
    {
        //$requestData = $request->validated();
        $return = ConstructionDetailsServices::uploadExcelForData($request);

        return  response()->data($return);
    }

    public function readExcel(Request $request)
    {
        //$requestData = $request->validated();
        importExcelToDB($_SERVER['DOCUMENT_ROOT']."/storage"."//"."1644231439Video-20220114_063635-Meeting.xlsx.xlsx");

        return  response()->success();
    }

    public function getPayToDetails(GetProjectDetialsFormRequest $request)
    {
        $requestData = $request->validated();
        $return = ConstructionDetailsServices::getPayToDetails($request);

        return  response()->data($return);
    }

    public static function getProjectConstructionDetails(GetProjectConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        $return = ConstructionDetailsServices::getProjectExcelForConstructionDetails($request);

        return  response()->data($return);
    }

    public static function deleteProject(GetProjectConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        ConstructionDetailsServices::deleteProject($request);

        return  response()->success();
    }

    public static function editConstructionDetails(EditConstructionDetailsFormRequest $request)
    {
        $requestData = $request->validated();
        ConstructionDetailsServices::editConstructionDetails($request);

        return  response()->success();
    }

    public static function getExportExcelProgress()
    {

        $return = ImportExcelTable::getProgress();

        return  response()->data(['progress' => $return]);
    }

    public static function ImportExcelJob(ImportExcelFileJobFormRequest $request)
    {
        $requestData = $request->validated();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 360);
        ImportExcel::dispatch($request['file_path'])->onQueue('high');

        return  response()->success();
    }
}
