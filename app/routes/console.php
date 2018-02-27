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

    $parcels = \App\Parcel::inRandomOrder()->limit(10)->get();

    foreach($parcels as $parcel){

        $transfers = $parcel->transfers()->get();
        $tcount = count($transfers);

        $mortgages = $parcel->mortgages();
        $mcount = count($mortgages);

        $file = fopen('/home/vagrant/Code/freethelots/test-data/parcel-'.$parcel->id.".txt", 'w');

        fwrite($file, PHP_EOL.'Parcel '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);

        fwrite($file, json_encode($parcel));
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
