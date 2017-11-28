<?php

namespace App\Http\Controllers;

use App\Deed;
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

        if(empty($search)){
            return false;
        }
        $search = str_replace(',', '' , $search);
        $search = strtoupper($search);

        /* Uses wildcards searches for partial match */
        $parties = DB::table('raw_source')
            ->select('grantee', 'document_date', 'document_type', 'raw_source.combined_legal')
            ->join('landbank', 'raw_source.combined_legal', 'landbank.combined_legal')
            ->Where('grantee', 'like', $search.'%' )
            ->limit(1000)->get();


        return $parties;
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
