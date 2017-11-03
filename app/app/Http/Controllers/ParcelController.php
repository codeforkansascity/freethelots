<?php

namespace App\Http\Controllers;

use App\Transfer;
use Illuminate\Http\Request;
Use DB;

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

       $parties = DB::table('deeds')
            ->select('*')
            ->where('grantee', 'like', $search.'%')
           ->limit(500)
            ->get();
        return $parties;
    }

}
