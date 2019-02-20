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
//Tag Routes
Route::get('tags/', 'TagController@list');
Route::post('tags/create', 'TagController@create');
Route::post('tags/edit/{id}', 'TagController@edit');
Route::delete('tags/delete/{id}', 'TagController@destroy');

// Task Tag routes
Route::get('tasks/tags/{id}', 'TaskController@listTags');
Route::post('tasks/removeTag/{id}/{tagID}', 'TaskController@removeTag');

// Task CRUD Routes
Route::get('tasks/', 'TaskController@index');
Route::post('tasks/create', 'TaskController@create');
Route::get('tasks/{id}', 'TaskController@read');
Route::put('tasks/update/{id}', 'TaskController@update');
Route::delete('tasks/delete/{id}', 'TaskController@delete');

// Task Control Routes
Route::post('tasks/{id}/start', 'TaskController@start');
Route::post('tasks/{id}/stop', 'TaskController@stop');
Route::post('tasks/{id}/finish', 'TaskController@finish');

// Token CRUD Routes
Route::get('tokens/', 'TokenController@index');
Route::post('tokens/create', 'TokenController@create');
Route::get('tokens/{id}', 'TokenController@read');
Route::put('tokens/update/{id}', 'TokenController@update');
Route::delete('tokens/delete/{token}', 'TokenController@delete');


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