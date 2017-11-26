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

       /* $split = explode(',', $search);

        /* Uses wildcards searches for partial match */
       /* if(count($split) > 1){
            $parties = DB::table('parcel')
                ->select('parcel.*', 'party.first_name', 'party.last_name', 'document_type.name', 'transfer.date_received'
                    ,'transfer.id as transfer_id')
                ->leftJoin('transfer', 'transfer.parcel_id', 'parcel.id')
                ->leftJoin('party', 'transfer.grantor_id', 'party.id')
                ->leftjoin('document_type', 'document_type.id', 'transfer.document_type_id')
                ->where(function($query) use($split) {
                    $query->where( 'party.first_name', 'ilike' , '%'.$split[0].'%' )
                    ->Where('party.last_name', 'ilike', '%'.$split[1].'%' );
                })
                ->get();
        }
        else{
            $parties = DB::table('parcel')
                ->select('parcel.* as parcel', 'party.first_name', 'party.last_name', 'document_type.name', 'transfer.date_received'
                        ,'transfer.id as transfer_id')
                ->leftJoin('transfer', 'transfer.parcel_id', 'parcel.id')
                ->leftJoin('party', 'transfer.grantor_id', 'party.id')
                ->leftJoin('document_type', 'document_type.id', 'transfer.document_type_id')
                ->where(function($query) use($search) {
                    $query->where( 'party.first_name', 'ilike' , '%'.$search.'%' )
                    ->orWhere('party.last_name', 'ilike', '%'.$search.'%' );
                })
                ->get();
        }*/

       $legals = DB::table('raw_source')
        ->select('deeds.*')
        ->leftJoin('landbank', 'deeds.combined_legal', 'landbank.combined_legal')
        ->where('grantee', 'like', $search.'%')
        ->limit(5000)
        ->get();

//       foreach($legals as $legal){
//           $parties = DB::table('deeds')
//               ->where('combined_legal', $legal)
//               ->where('grantor', 'like', $search.'%' )
//               ->get()->max('date_received');
//           if($parties){
//               return $parties;
//           }
//       }
//       $parties = DB::table('deeds')
//           ->whereIn('combined_legal', $legals)
//           ->get();

        return $legals;
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
