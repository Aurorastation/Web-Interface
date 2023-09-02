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

Route::group(['prefix' => 'server'], function () {
    Route::group(['prefix' => 'log'], function () {
        Route::post('/upload', ['as' => 'api.server.log.upload', 'uses' => 'Server\LogController@upload']);
        Route::post('/register', ['as' => 'api.server.log.register', 'uses' => 'Server\LogController@register']);
    });
});