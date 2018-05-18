<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Entity extends Model
{
    protected $table = 'entity';

    public function parcels(){

        return $parcels = DB::table('parcel_combined as pc')
            ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )
            ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')
            ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')
            ->join('entity as e', 'e.id', 'tparty.entity_id')
            ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')
            ->join('parcel as par', 'par.id', 'pc.parcel_id')
            ->where('e.id', $this->id);
        /* return $parcels = ParcelCombined::whereIn('id',
            DB::table('parcel_combined as pc')
                ->select('pc.id')
                ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )
                ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')
                ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')
                ->join('entity as e', 'e.id', 'tparty.entity_id')
                ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')
                ->join('parcel as par', 'par.id', 'pc.parcel_id')
                ->where('e.id', $this->id
                ) );*/

    }

    public function parcel_combined()
    {
        return $this->transfers->belongsToMany('App\ParcelCombined','transfer_parcel',   'transfer_id', 'parcel_id');
    }

    public function transfers(){

//        $transfers = $this->hasManyThrough(
//            'App\Transfer',
//            'App\TransferParty',
//            'entity_id', // Foreign key on users table...
//            'id', // Foreign key on posts table...
//            'id', // Local key on countries table...
//            'transfer_id' // Local key on users table...
//        );

        $transfers = $this->belongsToMany('App\Transfer','transfer_party',  'entity_id', 'transfer_id');

        return $transfers;
    }

    public function grantees(){
        return $parcels = DB::table('parcel_combined as pc')
            ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )
            ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')
            ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')
            ->join('entity as e', 'e.id', 'tparty.entity_id')
            ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')
            ->join('parcel as par', 'par.id', 'pc.parcel_id')
            ->where('e.id', $this->id)
            ->where('pt.name', 'grantee');
    }

    public function grantors(){
        return $parcels = DB::table('parcel_combined as pc')
            ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )
            ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')
            ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')
            ->join('entity as e', 'e.id', 'tparty.entity_id')
            ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')
            ->join('parcel as par', 'par.id', 'pc.parcel_id')
            ->where('e.id', $this->id)
            ->where('pt.name', 'grantor');
    }



}
