<?php

use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::group(['middleware' => ['api'/*,'checkPassword',*/,/*'changeLanguage'*/]], function () {

    Route::group(['prefix' => 'admin','namespace'=>'admin'],function (){
        Route::post('login', 'AuthController@login');

        Route::post('logout','AuthController@logout') -> middleware(['auth.guard:admin-api']);
        //invalidate token security side

        //broken access controller user enumeration
    });

    Route::group(['prefix' => 'user','namespace'=>'user'],function (){
        Route::post('register','AuthController@register') ;
        Route::post('logout','AuthController@logout') -> middleware(['auth.guard:user-api']);
        Route::post('login','AuthController@Login') ;

    });


    Route::group(['prefix' => 'user' ,'middleware' => 'auth.guard:user-api'],function (){
        Route::post('profile',function(){
            return  \Auth::user(); // return authenticated user data
        }) ;


    });

});


