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

        $split = explode(',', $search);

        /* Uses wildcards searches for partial match */
        if(count($split) > 1){
            $parties = DB::table('parcel')
                ->leftJoin('transfer', 'transfer.parcel_id', 'parcel.id')
                ->leftJoin('party', 'transfer.grantor_id', 'party.id')
                ->where(function($query) use($split) {
                    $query->where( 'party.first_name', 'like' , '%'.$split[0].'%' )
                    ->Where('party.last_name', 'like', '%'.$split[1].'%' );
                })
                ->get();
        }
        else{
            $parties = DB::table('parcel')
                ->leftJoin('transfer', 'transfer.parcel_id', 'parcel.id')
                ->leftJoin('party', 'transfer.grantor_id', 'party.id')
                ->where(function($query) use($search) {
                    $query->where( 'party.first_name', 'like' , '%'.$search.'%' )
                        ->orWhere('party.last_name', 'like', '%'.$search.'%' );
                })
                ->get();
        }

        return $parties;
    }

}
