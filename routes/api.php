<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['middleware'=> ['tracker']],function () {
        Route::group(['namespace' => 'Api\Auth'], function () {
          
                Route::post('login','UserController@userLogin');		
                Route::post('register','UserController@userSignUp');
                Route::get('user-list','UserController@getUserList');
                Route::post('update-user','UserController@updateUser');
                Route::post('forgot-password','UserController@forgotPassword');
                Route::post('decyprt-password','UserController@decryptPassword');
        });

        Route::group(['namespace' => 'Api\Auth'], function () {
                Route::post('get-user-projects','UserController@getUserProjectLinkingDetails');
                Route::post('link-user-project','UserController@linkUserAndProjects');
                Route::get('get-lookup-value','UserController@lookUpValue');
                Route::get('wages-number','ConstructionDetailsController@getWagesNumber');
                Route::get('get-project','ConstructionDetailsController@getProject');
                Route::post('get-project-details','ConstructionDetailsController@getProjectDetails');
                Route::post('get-block-details','ConstructionDetailsController@getBlockDetails');
                Route::post('get-floor-details','ConstructionDetailsController@getFloorDetails');
                Route::post('get-apartment-details','ConstructionDetailsController@getApartmentDetails');
                Route::post('get-construction-details','ConstructionDetailsController@getConstructionDetails');
                Route::post('total-construction-details','ConstructionDetailsController@getTotalProjectDetails');
                Route::post('get-description-work','ConstructionDetailsController@getDescriptionWork');
                Route::post('delete-project','ConstructionDetailsController@deleteProject');
                Route::post('get-pay-to-details','ConstructionDetailsController@getPayToDetails');
                Route::post('edit-construction-details','ConstructionDetailsController@editConstructionDetails');

                // wages booking
                Route::post('book-wages','WagesBookingController@bookWages');
                Route::post('add-wages-number','WagesBookingController@addWages');
                Route::post('edit-booked-wages','WagesBookingController@editBookedWages');
                Route::post('delete-booked-wages','WagesBookingController@deleteBookedWages');
                Route::post('final-wages-submission','WagesBookingController@finalSubmissionWages');
                Route::post('get-wages','WagesBookingController@getWages');
                Route::post('download-wages','WagesBookingController@downloadWages');
                Route::post('get-wages-excel','WagesBookingController@downloadWages');
                // Route::post('upload-videos','VideosController@uploadVideo');
                // Route::post('download-videos','VideosController@downloadVideo');
                Route::post('add-pay-details','WagesBookingController@addPayToDetails');
                Route::post('delete-pay-details','WagesBookingController@deletePayTODetails');
                Route::post('upload-pay-details','WagesBookingController@uploadPayTODetails');

                Route::get('clean-directory','UserController@test');
                Route::post('wages-controller','UserController@wagesPortalController');
                Route::post('get-expot-excel-progress','ConstructionDetailsController@getExportExcelProgress');
                Route::post('import-excel-job','ConstructionDetailsController@ImportExcelJob');

         
                Route::post('upload-excel','ConstructionDetailsController@uploadExelForConstructionDetails');
                Route::post('read-excel','ConstructionDetailsController@readExcel');
                Route::post('download-construction-details','ConstructionDetailsController@getProjectConstructionDetails');
                Route::post('import-main-excel','ConstructionDetailsController@ImportMainExcelJob');
         
            
        });

    });

        // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        //     return $request->user();
        // });

        // If API not found
        Route::fallback(function(){
            return response()->routeNotFound();
        });
