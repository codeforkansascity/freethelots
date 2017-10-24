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



Route::get('/transfers', 'TransferController@index');

Route::get('/parties', 'PartyController@index');
Route::get('/parties/search/{search}', 'PartyController@search');

Route::get('/parcels', 'ParcelController@index');
Route::get('/parcels/search/{search}', 'ParcelController@searchGrantor');





