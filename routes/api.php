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
Route::get('telegram', 'TelegramController@index');
Route::put('telegram/{id}', 'TelegramController@editTelega');
Route::get('telegramdel', 'TelegramController@delete');
Route::get('nonanswered', 'TelegramController@nonAnswered');
Route::get('answered', 'TelegramController@answered');
Route::get('testing', 'TestingController@index');
Route::delete('testing', 'TestingController@delete');