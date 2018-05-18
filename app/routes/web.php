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

Route::get('/entity/{entity}/parcels', 'ParcelController@searchByEntity');
Route::get('/entity/search/{name}', 'EntityController@searchByName');

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

        if(in_array($first, [ 'CIT','SBD', 'LT ', 'BLK'])){
            $parsed .= $part.';';
        }
    }
    $parsed = rtrim($parsed, ';');


    $regular_results = DB::table('raw_source')
        ->select('combined_legal')
        ->where('combined_legal',  $legal->combined_legal)
        ->orderby('date_received')
        ->get();


    $parsed_results =  DB::table('raw_source')
        ->select('combined_legal')
        ->where('combined_legal', 'like', $parsed.'%')
        ->get();

    $extra_results = $parsed_results->count() - $regular_results->count();
    $time_end = microtime(true);
    $time = round($time_end - $time_start, 2). ' sec';


//    $pc = \App\ParcelCombined::inRandomOrder()->first();
//    $mortgages = $pc->mortgages();
//    $transfers = $pc->transfers()->get();
//    return compact('pc', 'mortgages', 'transfers');

    return compact(  'explanation' ,'time','parsed', 'extra_results' , 'legal' ,'regular_results', 'parsed_results');
});

Route::get('/landbank', function (){
    // get only land bank deeds
    $deeds =  DB::table('raw_source')
        ->select('combined_legal')
        ->where('grantee',  'like', 'LAND BANK%')
        ->groupby('combined_legal')
        ->get();


    return $deeds;
});

Route::get('/test2/{type}', function ($type){


    $time_start = microtime(true);
    // get all records of single property
    $type = !empty($type)? strtolower($type): '';

    $low = rand(1, 1000);
    $legal = DB::table('landbank')
        ->select('*');
    if($type == 'section'){
        $legal->whereNull('subdivision')
        ->where('combined_legal', 'like', '%SEC%');
    }elseif ($type == 'subdivision'){
        $legal->whereNotNull('subdivision');
    }
    else{
        $legal->whereNull('subdivision')
            ->where('combined_legal', 'not like', '%SEC%');
    }

    $legal = $legal->inRandomOrder()->first();

    if(empty($legal)){
        dd('empty');
    }

    // subdivisions
    $city = '';
    $subdivision = '';
    $block = '';
    $lot = '';
    $frontage  = '';
    // sections
    $town = '';
    $section = '';
    $range = '';
    $qtr1 = '';
    $qtr2 = '';

    $is_subdivision = preg_match('/SBD[^;]*/', $legal->combined_legal, $subdivision);
    $is_section = preg_match('/SEC[^;]*/', $legal->combined_legal, $section);
    if(!empty($subdivision)) {
        preg_match('/CITY[^;]*/', $legal->combined_legal, $city);
        preg_match('/BLK[^;]*/', $legal->combined_legal, $block);
        preg_match('/LT \d+[-]?\d*/', $legal->combined_legal, $lot);
        preg_match("/ [NEWS][EW]? [1-9][0-9]*[ \.''\/][0-9]*['']?/", $legal->combined_legal, $frontage);
        $legal_extract = compact('city','block', 'subdivision', 'lot', 'frontage');
    }
    else if(!empty($section)){
        preg_match('/TWN[^;]*/', $legal->combined_legal, $town);
        preg_match('/RNG[^;]*/', $legal->combined_legal, $range);
        preg_match('/Q1 [^;]*/', $legal->combined_legal, $qtr1);
        preg_match('/Q2 [^;]*/', $legal->combined_legal, $qtr2);
        preg_match("/[NEWS]?[EW]? ?[1-9][0-9]* ?[\.''\/]+[0-9]*['']? ?[NEWS]?[EW]?/", $legal->combined_legal, $frontage);
        $legal_extract = compact('town','section', 'range', 'qtr1', 'qtr2', 'frontage');
    }
//    dd(compact('legal_extract', 'legal'));

    $points = 0;
    foreach($legal_extract as $item){
        if(!empty($item)){
            $points++;
        }
    }
    if($points < 2){
        $message  = "Less than 2 items identified";
        dd(compact('legal_extract', 'legal', 'message'));
    }


    $parsed_results =  DB::table('raw_source')
        ->select('*');


    /* Subdivision */
    if( !empty($subdivision[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$subdivision[0].'%');

    }
    if( !empty($lot[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$lot[0].'%');

    }
    if( !empty($frontage[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$frontage[0].'%');

    }

    /* Section */
    if( !empty($section[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$section[0].'%');

    }
    if( !empty($town[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$town[0].'%');

    }
    if( !empty($range[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$range[0].'%');

    }
    if( !empty($qtr1[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$qtr1[0].'%');

    }
    if( !empty($qtr2[0]) ){
        $parsed_results = $parsed_results->where('combined_legal', 'like', '%'.$qtr2[0].'%');

    }

    $parsed_results = $parsed_results->get();
        //->where('combined_legal', 'like', '%'.$block[0].'%')




    $time_end = microtime(true);
    $time = round($time_end - $time_start, 2). ' sec';

    return compact(  'time','legal', 'legal_extract' , 'parsed_results' );
});

Route::post('/csv', 'ParcelController@convertToCsv');

Route::get('/transfers/{id}', function($id){
    $parcel = \App\Parcel::find($id);

    $transfers = $parcel->transfers()->get();
    $count = count($transfers);

    return compact('count', 'transfers');
});

Route::get('/mortgages/{id}', function($id){
    $parcel = \App\Parcel::find($id);

    $mortgages = $parcel->mortgages();
    $count = count($mortgages);
    return compact('count','mortgages');
});


Route::get('/random/', function(){
    
    $parcel = \App\Parcel::inRandomOrder()->first();

    $transfers = $parcel->transfers()->get();
    $tcount = count($transfers);

    $mortgages = $parcel->mortgages();
    $mcount = count($mortgages);

    return compact('parcel', 'tcount', 'transfers','mcount','mortgages');
});

Route::get('/landbank', function(){

    $aliases = [
        'LAND BANK KANSAS CITY',
//        'LAND BANK OF BLUE SPRINGS',
//        'LAND BANK OF BLUE SPRINGS MISSOURI',
        'LAND BANK OF KANAS CITY MISSOURI',
        'LAND BANK OF KANSAS CITY',
        'LAND BANK OF KANSAS CITY  MISSOURI',
        'LAND BANK OF KANSAS CITY MISOURI',
        'LAND BANK OF KANSAS CITY MISSOUI',
        'LAND BANK OF KANSAS CITY MISSOUIR',
        'LAND BANK OF KANSAS CITY MISSOURI',
        'LAND BANK OF KANSAS CITY MO',
        'LAND BANK OF KANSAS CITY MSSOURI',
        'LAND BANK OF KCMO',
        'LAND BANK OFKANSAS CITY MISSOURI',
        'LAND BANK OK KANSAS CITY',
        'LAND BANK'
    ];

    $entities = \App\Entity::whereIn('name', $aliases)->get();


    $parcels = \App\Parcel::allParcels($entities)->get();
    return dd($parcels);
});

Route::get('/sample-transfer', function(){

    $landbank = \App\LandbankParcel::inRandomOrder()->first();
    $transfers = $landbank->parcel->transfers()->orderBy('date')->get();
    $tcount = count($transfers);

    $mortgages = $landbank->parcel->mortgages();
    $mcount = count($mortgages);

    return compact('landbank', 'transfers', 'tcount', 'mortgages', 'mccount');
});

Route::get('/sample-transfer/{id}', function($id){

    $parcel = \App\Parcel::find($id);
    $transfers = $parcel->transfers()->orderBy('date')->get();
    $tcount = count($transfers);

    $mortgages = $parcel->mortgageHistory(true);
    $mcount = count($mortgages);

    return compact('landbank', 'transfers', 'tcount', 'mortgages', 'mcount');
});

Route::get('/current-landbank', function(){

    $ids = DB::table('entity')->where('name', 'like', '%LAND BANK%')->get()->pluck('id')->toArray();

    $parcels = \App\Parcel::whereIn('id', \App\LandbankParcel::get()->pluck('parcel_id')->toArray())
        ->limit(50)->get();


    //dd($parcels);
    $list = [];
    foreach($parcels as $parcel){

        $current = true;
        $transfers = $parcel->transfers()->orderBy('date')->get();
        foreach($transfers as $transfer){
            if(in_array($transfer->entity_id, $ids) && $transfer->type == 'grantor'){
                $current = false;
            }

        }

        if($current){
            $list[] = $parcel;
        }
    }
    return $list;

});

/*
 "type": "grantee"
  +"name": "LAND BANK OF KANSAS CITY MISSOURI"
  +"entity_id": 666531
  +"description": "CITY UNKNOWN CODES; SBD LAMBERT BROTHERS ADD TO LAMBERT GROVE 05-3064 KC; LT 11-11"
  +"doc_type": "WD"
  +"date": "2013-05-01 00:00:00"
  +"t_id": 1907721
 **/




