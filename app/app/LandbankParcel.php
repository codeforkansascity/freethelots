<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LandbankParcel extends Model
{
    protected $table = 'landbank_parcel';

    protected function parcel(){
        return $this->belongsTo('App\Parcel', 'parcel_id', 'id');
    }
}
