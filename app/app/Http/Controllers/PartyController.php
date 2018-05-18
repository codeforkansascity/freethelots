<?php

namespace App\Http\Controllers;

use App\Party;
use Illuminate\Http\Request;
use DB;
use PhpParser\Node\Expr\Cast\Object_;

class PartyController extends Controller
{
    protected function  index(){

        $party = Party::all();

        return $party;

    }

    protected function search($search){

        //$split = explode(',', $search);
        $parcel = DB::table('transfer')
            ->join('transfer_party', 'transfer.id', 'transfer_party.transfer_id')
            ->join('transfer_parcel', 'transfer.id', 'transfer_parcel.transfer_id')
            ->join('entity', 'transfer_parcel.entity_id', 'entity.id')
            ->where('entity.name', $search)
            ->get();
        
        /* Uses case insensitive wildcards searches for partial match */
//        if(count($split) > 1){
//            $parties = Party::where( 'first_name', 'ilike' , '%'.$split[0].'%' )->orWhere('last_name', 'ilike', '%'.$split[1].'%' )->get();
//        }
//        else{
//            $parties = Party::where( 'first_name', 'ilike' , '%'.$split[0].'%' )->orWhere('last_name', 'ilike', '%'.$split[0].'%' )->get();
//        }

        dd($parcel);
        return $parcel;
    }
}
