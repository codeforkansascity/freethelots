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

Artisan::command('sample-transfers', function(){

    $landbanks = \App\LandbankParcel::inRandomOrder()->limit(10)->get();

    foreach($landbanks as $landbank){

        $transfers = $landbank->parcel->transfers()->orderBy('date')->get();
        $tcount = count($transfers);

        $mortgages = $landbank->parcel->mortgages();
        $mcount = count($mortgages);

        $file = fopen('/home/vagrant/Code/freethelots/test-data/parcel-'.$landbank->parcel->id.".txt", 'w');

        fwrite($file, PHP_EOL.'Parcel '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);

        fwrite($file, json_encode($landbank->parcel));
        fwrite($file, PHP_EOL.PHP_EOL);

        fwrite($file, PHP_EOL.'Parcel Transfers '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);
        foreach ($transfers as $transfer){
            fwrite($file, json_encode($transfer));
            fwrite($file, PHP_EOL.PHP_EOL);
        }
        fwrite($file, PHP_EOL.'Active Mortgages '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);
        foreach($mortgages as $mortgage){
            fwrite($file, json_encode($mortgage));
            fwrite($file, PHP_EOL.PHP_EOL);

        }
        fclose($file);

    }


    //return compact('parcel', 'tcount', 'transfers','mcount','mortgages');
});

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
