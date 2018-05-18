<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

// create 10 sample mortgage histories
Artisan::command('sample-transfers', function(){

    $landbanks = \App\LandbankParcel::inRandomOrder()->limit(10)->get();
    foreach($landbanks as $landbank){
        $landbank->parcel->mortgageHistory(true);
    }

});

// insert landbank parcel into table
Artisan::command('landbank', function(){

    $entity = \App\Entity::find(666531);//('name', 'like', 'LAND BANK%')->get();

    //$parcels = \App\Parcel::allParcels($entities)->get();

    $parcels = $entity->parcels()->get();
    //dd($parcels[0]);
    dd($parcels[0]->transfers());
    foreach($parcels as $parcel){

        if(empty(DB::table('landbank_parcel')->where('parcel_id', $parcel->entity_id)->first() ) ){
            info('inserting id: '. $parcel->parcel_id);
            DB::table('landbank_parcel')
                ->insert([
                    'parcel_id' => $parcel->parcel_id,
                ]);
        }

    }


});

Artisan::command('current-landbank', function(){

    $this->info('Checking...');


    $dtStart = \Carbon\Carbon::now();

    $ids = DB::table('entity')->where('name', '=', 'LAND BANK OF KANSAS CITY MISSOURI')
        ->get()->pluck('id')->toArray();

    // id = 666531

    $parcels = \App\Parcel::whereIn('id', \App\LandbankParcel::get()->pluck('parcel_id')->toArray())->limit(300)
        ->get();


    //dd($parcels);
    $list = [];
    foreach ($parcels as $parcel) {

        $current = true;
        $transfers = $parcel->transfers()->orderBy('date')->get();
        foreach ($transfers as $transfer) {


            if (in_array($transfer->entity_id, $ids) && $transfer->type == 'grantor' && $transfer->doc_type == 'WD') {
                $current = false;
            }

        }

        if ($current) {
            $this->info('found record'. $parcel->id);

            $list[] = ['parcel_id' => $parcel->id];
        }
    }

    dd($list);
    DB::table('landbank_current')->insert($list);
    $dtEnd = \Carbon\Carbon::now();
    $diff = $dtEnd->diffInSeconds($dtStart);
    $this->info(count($list).' Records written in '. $diff. ' seconds');


    return $list;


});

Artisan::command('landbank-mortgages', function (){
    $this->info('Checking...');
    $ids = DB::table('landbank_current')->get()->pluck('parcel_id')->toArray();
    //dd($ids);
    $parcels = \App\Parcel::whereIn('id', $ids)->get();

    foreach($parcels as $parcel){
        $this->info('Checking... '. $parcel->id);

        $mortgages = $parcel->mortgageHistory(true);

        $this->info('Found '. count($mortgages). ' mortgages');

    }

});
