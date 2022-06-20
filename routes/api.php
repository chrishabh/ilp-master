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

        Route::group(['namespace' => 'Api\Auth'], function () {
            Route::post('login','UserController@userLogin');		
            Route::post('register','UserController@userSignUp');
            Route::get('user-list','UserController@getUserList');
            Route::post('update-user','UserController@updateUser');
            
        });

        Route::group(['namespace' => 'Api\Auth'], function () {
            //Route::group(['middleware' => ['userAuth']], function () {
                Route::get('get-lookup-value','UserController@lookUpValue');
                Route::post('get-project-details','ConstructionDetailsController@getProjectDetails');
                Route::post('get-block-details','ConstructionDetailsController@getBlockDetails');
                Route::post('get-apartment-details','ConstructionDetailsController@getApartmentDetails');
                Route::post('get-construction-details','ConstructionDetailsController@getConstructionDetails');
                Route::post('get-description-work','ConstructionDetailsController@getDescriptionWork');
                Route::post('delete-project','ConstructionDetailsController@deleteProject');
                Route::post('get-pay-to-details','ConstructionDetailsController@getPayToDetails');
                Route::post('edit-construction-details','ConstructionDetailsController@editConstructionDetails');

                // wages booking
                Route::post('book-wages','WagesBookingController@bookWages');
                Route::post('edit-booked-wages','WagesBookingController@editBookedWages');
                Route::post('delete-booked-wages','WagesBookingController@deleteBookedWages');
                Route::post('final-wages-submission','WagesBookingController@finalSubmissionWages');
                Route::post('get-wages','WagesBookingController@getWages');
                Route::post('download-wages','WagesBookingController@downloadWages');
                Route::post('get-wages-excel','WagesBookingController@downloadWages');
                // Route::post('upload-videos','VideosController@uploadVideo');
                // Route::post('download-videos','VideosController@downloadVideo');
                Route::post('add-pay-details','WagesBookingController@addPayToDetails');

                Route::get('clean-directory','UserController@test');
                Route::post('get-expot-excel-progress','ConstructionDetailsController@getExportExcelProgress');
                Route::post('import-excel-job','ConstructionDetailsController@ImportExcelJob');

            //});
            Route::post('upload-excel','ConstructionDetailsController@uploadExelForConstructionDetails');
            Route::post('read-excel','ConstructionDetailsController@readExcel');
            Route::post('download-construction-details','ConstructionDetailsController@getProjectConstructionDetails');
            
        });

        // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        //     return $request->user();
        // });

        // If API not found
        Route::fallback(function(){
            return response()->routeNotFound();
        });
