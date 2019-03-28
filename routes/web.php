<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::group(['prefix' => 'api/v1', 'middleware' => ['cors', 'api']], function () {
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::post('register', 'AuthenticateController@register');
    Route::group(['middleware' => ['jwt.auth']], function () {
        Route::get('reports/', 'ReportController@index');
        Route::get('reports/{id}', 'ReportController@show');
        Route::post('reports/store', 'ReportController@store');
        Route::post('reports/update/{id}', 'ReportController@update');
        Route::get('reports/destroy/{id}', 'ReportController@destroy');
    });


});
