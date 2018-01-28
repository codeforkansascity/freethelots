<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $table = 'parcel';

    public function parties(){
        //return $this->hasManyThrough(); transfers, transfer_party, party
    }

    public function transfers(){

        return $parcels = DB::table('parcel_combined as pc')
            ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )
            ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')
            ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')
            ->join('entity as e', 'e.id', 'tparty.entity_id')
            ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')
            ->join('parcel as par', 'par.id', 'pc.parcel_id')
            ->where('pc.id', $this->id)
            ->get();
    }
}
