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

Route::get('/test', function (){

    $time_start = microtime(true);
    // get all records of single property

    $low = rand(1, 1000);
    $legal = DB::table('landbank')
        ->select('*')
        ->where('id',$low)
        ->first();

    if(empty($legal)){
        dd('empty');
    }

    $parts = explode( ';', $legal->combined_legal);
    $parsed = '';
    foreach($parts as $part){

        $first = substr(trim($part), 0, 3);

        if(in_array($first, [ 'SBD', 'LT ', 'BLK'])){
            $parsed .= $part.';';
        }
    }
    $parsed = rtrim($parsed, ';');


    $regular_count = DB::table('deeds')
        ->select('combined_legal')
        ->where('combined_legal',  $legal->combined_legal)
        ->orderby('date_received')
        ->get();


    $parsed_count =  DB::table('deeds')
        ->select('combined_legal')
        ->where('combined_legal', 'like', '%'.$parsed.'%')
        ->get();

    $extra_results = $parsed_count->count() - $regular_count->count();
    $time_end = microtime(true);
    $time = round($time_end - $time_start, 2). ' sec';

    return compact( 'time','parsed', 'extra_results' , 'legal' ,'regular_count', 'parsed_count');
});

Route::get('/landbank', function (){
    // get only land bank deeds
    $deeds =  DB::table('deeds')
        ->select('combined_legal')
        ->where('grantee',  'like', 'LAND BANK%')
        ->groupby('combined_legal')
        ->get();


    return $deeds;
});





