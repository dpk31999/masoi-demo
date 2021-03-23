<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/test/{id}','TestController@get');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/room/{id}','RoomController@index')->name('room');
Route::get('/outroom/{id}','RoomController@outRoom')->name('outroom');

Route::post('/message/{room}', 'MessageController@store')->name('message.store');

Route::get('/room/{room}/type/{type}','MessageController@getByType');
