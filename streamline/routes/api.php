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


Route::post('tasks', 'TaskController@store');

/* Tag Routes -------*/
Route::post('tags', 'TagController@store');
Route::get('tags', 'TagController@list');
Route::delete('tags/{id}', 'TagController@destroy');
Route::post('tags/{id}', 'TagController@edit');
/* ---------------- */
/* Setting Routes -------*/
Route::put('settings', 'SettingsController@edit');
Route::get('settings', 'SettingsController@list');
/* --------------------- */
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
    Route::post('delete', 'AuthController@delete');


});
