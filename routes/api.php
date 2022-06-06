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

Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\RegisterController@login');
Route::get('login', 'API\RegisterController@login')->name('login');

Route::middleware('auth:api')->group( function () {
	Route::get('apicalls', 'API\ApicallsController@index');
    Route::post('apicalls', 'API\ApicallsController@store');
    Route::post('/mapboxcall', 'API\ApicallsController@mapboxcall');

    Route::get('apicallsoutput', 'API\ApicallsOutputController@index');
    Route::post('apicallsoutput', 'API\ApicallsOutputController@store');
});
