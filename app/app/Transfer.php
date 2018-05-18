<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfer';

    public function grantees(){

    }
    public function entities(){

    }
    public function grantors(){

    }

    public function parcel_combined()
    {
        return $this->belongsToMany('App\ParcelCombined','transfer_parcel',   'transfer_id', 'parcel_id');

    }
}
