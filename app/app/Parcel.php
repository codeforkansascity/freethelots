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

    public function mortgages($write = false){
        $t_count = 1;

        $transfers = $this->transfers()->orderBy('date')->get();
        //dd($transfers);

        // get all mers alias ids
        $merd_ids = Entity::where('name', 'like', '%MORTGAGE ELEC%')->get()->pluck('id')->toArray();

        $active = [];
        foreach ($transfers as $transfer){
            $released = false;

            if($transfer->type == 'grantee' && in_array($transfer->doc_type ,['DT', 'ASDT']) ){

                $t_count++;

                // if deed of trust mortgage is released
                $trustee_deed = $transfers->where('doc_type', 'TD')->where('date', '>', $transfer->date)->first();
                if(!empty($trustee_deed)){

                    $released = true;
                }

                //if mers
                $mers = $transfers->whereIn( 'entity_id', $merd_ids)->where('doc_type', 'ASDT')
                    ->where('date', '>', $transfer->date)->first();
                if(!empty($mers)){
                    if(count($active) < 1){

                        $released = true;
                    }
                }

                //dd($mers);
                // if grantee matches a grantor of future date
                $matches = $transfers->filter(function($key) use($transfer){
                    $transfer_clipped = str_replace(' NA', '',$transfer->name);
                    $clipped = str_replace(' NA', '',$key->name);
                    if($transfer_clipped == $clipped && $key->date >= $transfer->date){
                        return true;
                    }else{
                        return false;
                    }
                });

                if(!empty($matches) && !$released){
                    foreach($matches as $match){
                        // if not same entry
                        if($match->t_id != $transfer->t_id && $match->type != $transfer->type &&
                            in_array($match->doc_type, ['WD', 'DT', 'REL', 'ASDT'])){

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

    public function mortgageHistory($write = false){
        $history = [];
        $t_count = 1;

        $transfers = $this->transfers()->orderBy('date')->get();
        //dd($transfers);

        // get all mers alias ids
        $merd_ids = Entity::where('name', 'like', '%MORTGAGE ELEC%')->get()->pluck('id')->toArray();

        $active = [];
        foreach ($transfers as $transfer){
            $released = false;

            if($transfer->type == 'grantee' && in_array($transfer->doc_type ,['DT', 'ASDT']) ){

                $history[] = "$t_count : $transfer->name is $transfer->type on $transfer->date type $transfer->doc_type ";
                $t_count++;

                // if deed of trust mortgage is released
                $trustee_deed = $transfers->where('doc_type', 'TD')->where('date', '>', $transfer->date)->first();
                if(!empty($trustee_deed)){

                    $released = true;

                    $history[] =  "\t$transfer->name is released with $trustee_deed->name on $trustee_deed->date type: $trustee_deed->doc_type ";

                }

                 //if mers
                $mers = $transfers->whereIn( 'entity_id', $merd_ids)->where('doc_type', 'ASDT')
                    ->where('date', '>', $transfer->date)->first();
                if(!empty($mers)){
                    if(count($active) < 1){

                        $released = true;

                        $history[] =  "\t$mers->name is released with $mers->name on $mers->date type: $mers->doc_type ";
                    }
                }

                //dd($mers);
                // if grantee matches a grantor of future date
                $matches = $transfers->filter(function($key) use($transfer){
                    $transfer_clipped = str_replace(' NA', '',$transfer->name);
                    $clipped = str_replace(' NA', '',$key->name);
                    if($transfer_clipped == $clipped && $key->date >= $transfer->date){
                        return true;
                    }else{
                        return false;
                    }
                });
                //$matches = $transfers->where('name', $transfer->name)->where('date', '>', $transfer->date);

                if(!empty($matches) && !$released){
                    foreach($matches as $match){
                        // if not same entry
                        if($match->t_id != $transfer->t_id && $match->type != $transfer->type &&
                            in_array($match->doc_type, ['WD', 'DT', 'REL', 'ASDT'])){

                            $released = true;
                            $history[] =  "\t$transfer->name is released with $match->name on $match->date type: $match->doc_type match";


                        }
                        //if($match->doc_type == 'TD' && $match->doc)
                    }
                }

                if(!$released){
                    $active[] = $transfer;
                    $history[] =  "\t$transfer->name is not released";

                }
            }

        }
        $parcel = $this->id;
        $transfers = $transfers->toArray();
        if($write) $this->writeResults($transfers, $history, $active);
        return $active;
    }

    public function writeResults($transfers, $history, $active)
    {

        // Combine transfers
        $combined_transfer = collect();
        //dd($transfers);
        foreach($transfers as $transfer){

            if(empty($combined_transfer->where('t_id', $transfer->t_id)->count() ) ){
                $temp = [];
                $id = $transfer->t_id;
                $matches = array_where($transfers, function($key) use($id){
                   if($key->t_id == $id) {
                       return true;
                   }else{
                       return false;
                   }
                });

                foreach ($matches as $match){
                    $temp['date'] = empty($temp['date'])? $match->date: $temp['date'];
                    $temp['t_id'] = empty($temp['t_id'])? $match->t_id: $temp['t_id'];
                    $temp['doc_type'] = empty($temp['doc_type'])? $match->doc_type: $temp['doc_type'];
                    $temp['grantee'] = $match->type == 'grantee'? $match->name: $temp['grantee'] ?? '';
                    $temp['grantor'] = $match->type == 'grantor'? $match->name: $temp['grantor'] ?? '';
                    $temp['description'] = empty($temp['description'])? $match->description: $temp['description'];

                }
                $combined_transfer->push($temp);
            }

        }
        $transfers = $combined_transfer;
        //$transfers = $landbank->parcel->transfers()->orderBy('date')->get();
        $tcount = count($transfers);

        $mortgages = $active;
        $mcount = count($mortgages);

        $file = fopen('/home/vagrant/Code/freethelots/test-data/parcel-'.$this->id."-history.txt", 'w');

        fwrite($file, PHP_EOL.'Parcel '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);

        //fwrite($file, json_encode($this));

        foreach($this->toArray() as $key => $value){
            fwrite($file, "\t".$key.' => '. $value);
            fwrite($file, PHP_EOL);
        }
        fwrite($file, PHP_EOL);

        fwrite($file, PHP_EOL.PHP_EOL);

        fwrite($file, PHP_EOL.'Parcel Transfers '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);
        $cnt =1;
        foreach ($transfers as $transfer){
            fwrite($file, $cnt.': '.PHP_EOL);
            foreach($transfer as $key => $value){
                fwrite($file, "\t".$key.' => '. $value);
                fwrite($file, PHP_EOL);
            }
            fwrite($file, PHP_EOL);

            $cnt++;

        }

        fwrite($file, PHP_EOL.'Release history  '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);
        foreach($history as $release){
            fwrite($file, $release);
            fwrite($file, PHP_EOL.PHP_EOL);

        }
        fwrite($file, PHP_EOL);

        fwrite($file, PHP_EOL.'Active Mortgages '.PHP_EOL.str_repeat("-=", 40).PHP_EOL.PHP_EOL);
        $cnt = 1;
        foreach($mortgages as $mortgage){
            fwrite($file, $cnt.':'.PHP_EOL);
            foreach($mortgage as $key => $value){
                fwrite($file, "\t".$key.' => '. $value);
                fwrite($file, PHP_EOL);
            }
            fwrite($file, PHP_EOL.PHP_EOL);

            $cnt++;

        }
        fclose($file);


    }

    public function splitParcels()
    {
        return DB::table('parcels');
    }

    public static function allParcels(Collection $entities)
    {
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