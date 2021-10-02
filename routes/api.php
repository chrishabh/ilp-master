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
            
        });

        Route::group(['namespace' => 'Api\Auth'], function () {
           // Route::group(['middleware' => ['auth:api']], function () {
                Route::post('get-project-details','ConstructionDetailsController@getProjectDetails');
                Route::post('get-block-details','ConstructionDetailsController@getBlockDetails');
                Route::post('get-apartment-details','ConstructionDetailsController@getApartmentDetails');
                Route::post('get-construction-details','ConstructionDetailsController@getConstructionDetails');
                Route::post('get-description-work','ConstructionDetailsController@getDescriptionWork');

                // wages booking
                Route::post('book-wages','WagesBookingController@bookWages');
                Route::post('get-wages','WagesBookingController@getWages');
                Route::post('get-wages-excel','WagesBookingController@getWagesExcel');
           // });
            
        });

        // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        //     return $request->user();
        // });

        // If API not found
        Route::fallback(function(){
            return response()->routeNotFound();
        });
