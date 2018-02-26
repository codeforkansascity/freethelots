<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ParcelCombined extends Model
{
    protected $table = 'parcel_combined';

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

            ->where('par.id', $this->id)
            ->orderBy('date');
        //->groupBy('t.id');
    }

    public function mortgages(){
        $transfers = $this->transfers()->get();
        $active = [];
        foreach ($transfers as $transfer){
            $released = false;

            if($transfer->type == 'grantee' && $transfer->doc_type == 'DT'){
                $matches = $transfers->where('name', $transfer->name);

                if(!empty($matches)){
                    foreach($matches as $match){
                        if($match->t_id != $transfer->t_id && $match->type != $transfer->type ){

                            $released = true;

                        }
                    }
                }

                if(!$released){
                    $active[] = $transfer;
                }
            }

        }
        return $active;
    }
}
