<?php

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

Route::get('tasks/', 'TaskController@index');
Route::post('tasks/', 'TaskController@create');
Route::get('tasks/{id}', 'TaskController@read');
Route::put('tasks/{id}', 'TaskController@update');
Route::delete('tasks/{id}', 'TaskController@delete');

Route::post('tasks/{id}/start', 'TaskController@start');
Route::post('tasks/{id}/stop', 'TaskController@stop');
Route::post('tasks/{id}/finish', 'TaskController@finish');

// authentication routes
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});