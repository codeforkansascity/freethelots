<?php

namespace App\Http\Controllers;

use App\Deed;
use App\Entity;
use App\Transfer;
use Illuminate\Http\Request;
Use DB;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class ParcelController extends Controller
{
    protected function index(){

        $parcels = DB::table('parcel')->get();

        return $parcels;

    }
    protected function searchGrantor($search){

        $search = strtoupper($search);

        $entity = Entity::where('name', 'like', $search.'%')->first();

        return $entity->parcels();


    }
    protected function searchByEntity(Entity $entity){

        return $entity->parcels();

    }

    protected function convertToCSV($request){

        dd($request);
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne(\Schema::getColumnListing('deeds'));
        $deeds = Deed::where('grantee', 'like', 'Land Bank%')
            ->limit(500)->get();
        $csv->insertAll($deeds->toArray());

        $csv->output('deeds.csv');

    }

}
