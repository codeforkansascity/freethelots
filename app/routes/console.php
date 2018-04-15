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

    $entities = \App\Entity::where('name', 'like', 'LAND BANK%')->get();

    $parcels = \App\Parcel::allParcels($entities)->get();

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
