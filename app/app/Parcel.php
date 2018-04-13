<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DB;

class Parcel extends Model
{
    protected $table = 'parcel';

    public function entities(){
        //return $this->hasManyThrough(); transfers, transfer_party, party
    }

   /* public function transfers(){
        return $this->belongsToMany('App\Transfer', 'transfer_parcel', 'transfer_id', 'parcel_id');
    }*/

    public function transfers(){

        return $parcels = DB::table('parcel_combined as pc')

            ->select( 'pt.name as type' ,'e.name', 'e.id as entity_id', 'pc.description', 'document_type.name as doc_type'
                        ,'t.document_date as date', 't.id as t_id'
            )

            ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )

            ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')

            ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')

            ->join('entity as e', 'e.id', 'tparty.entity_id')

            ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')

            ->join('parcel as par', 'par.id', 'pc.parcel_id')

            ->join('document_type', 'document_type.id', 't.document_type_id')

            ->where('par.id', $this->id);
            //->groupBy('t.id');
    }

    public function mortgages(){

        $transfers = $this->transfers()->get();
        //dd($transfers);

        $merd_ids = Entity::where('name', 'like', '%MORTGAGE ELEC%')->get()->pluck('id')->toArray();

        $active = [];
        foreach ($transfers as $transfer){
            $released = false;

            if($transfer->type == 'grantee' && in_array($transfer->doc_type ,['DT', 'WD']) ){


                // if deed of trust mortgage is released
                $trustee_deed = $transfers->where('doc_type', 'TD')->where('date', '>', $transfer->date);
                if($trustee_deed){

                    $released = true;

                }

                 //if mers
                $mers = $transfers->whereIn( 'entity_id', $merd_ids)->where('date', '>', $transfer->date);

                //dd($mers);
                // if grantee matches a grantor of future date
                $matches = $transfers->where('name', $transfer->name)->where('date', '>', $transfer->date);

                if(!empty($matches) && !$released){
                    foreach($matches as $match){
                        // if not same entry
                        if($match->t_id != $transfer->t_id && $match->type != $transfer->type ){

                            $released = true;

                        }
                        //if($match->doc_type == 'TD' && $match->doc)
                    }
                }

                if(!$released){
                    $active[] = $transfer;
                }
            }

        }
        return $active;
    }

    public function splitParcels(){
        return DB::table('parcels');
    }

    public static function allParcels(Collection $entities){
        //dd($entities->pluck('id'));
        $parcels = DB::table('parcel_combined as pc')

            ->select( 'pt.name as type' ,'e.name', 'e.id as entity_id', 'pc.description', 'document_type.name as doc_type'
                ,'t.document_date as date', 't.id as t_id', 'par.id as parcel_id'
            )

            ->join('transfer_parcel', 'transfer_parcel.parcel_id', 'pc.id' )

            ->join('transfer as t', 't.id', 'transfer_parcel.transfer_id')

            ->join('transfer_party as tparty', 'tparty.transfer_id', 't.id')

            ->join('entity as e', 'e.id', 'tparty.entity_id')

            ->join('party_type as pt', 'pt.id', 'tparty.party_type_id')

            ->join('parcel as par', 'par.id', 'pc.parcel_id')

            ->join('document_type', 'document_type.id', 't.document_type_id')

            ->whereIn('e.id', $entities->pluck('id') );

        return $parcels;
    }

}

/*
 *
 * select pc.id, t.document_date, t.instrument_number, e.name, pt.name
from parcel_combined pc
inner join transfer_parcel tparcel on tparcel.parcel_id = pc.id

inner join transfer t on t.id = tparcel.transfer_id

inner join transfer_party tparty on tparty.transfer_id = t.id

inner join entity_dirty e on e.id = tparty.entity_id

inner join party_type pt on pt.id = tparty.party_type_id

inner join parcel as par on par.id = pc.parcel_id

where par.id = (select id from parcel where subdivision like 'RUSKIN VILLAGE%' and lot like '83%')
order by t.document_date;
*/