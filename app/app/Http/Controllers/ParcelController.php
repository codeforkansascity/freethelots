<?php

namespace App\Http\Controllers;

use App\Deed;
use App\Entity;
use App\Parcel;
use App\ParcelCombined;
use App\Transfer;
use Illuminate\Http\Request;
Use DB;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class ParcelController extends Controller
{
    protected function index()
    {

        $parcels = DB::table('parcel')->get();

        return $parcels;

    }

    protected function show(Parcel $parcel)
    {
        return response()->json(['parcel' => $parcel ], 200 );

    }
    protected function searchGrantor($search)
    {

        $search = strtoupper($search);

        $entity = Entity::where('name', 'like', $search . '%')->first();


        return $entity->parcels()->get();


    }

    protected function searchByEntity(Entity $entity)
    {

        return $entity->parcels();

    }

    protected function convertToCSV($request)
    {


        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne(\Schema::getColumnListing('deeds'));
        $deeds = Deed::where('grantee', 'like', 'Land Bank%')
            ->limit(500)->get();
        $csv->insertAll($deeds->toArray());

        $csv->output('output.csv');

    }

    protected function mortgages(Parcel $parcel)
    {

        return response()->json(['parcel' => $parcel, 'mortgages' => $parcel->mortgages() ], 200);
    }

    protected function transfers(Parcel $parcel)
    {
        return response()->json(['parcel' => $parcel , 'transfers' => $parcel->transfers()->get()] , 200);
    }

}
