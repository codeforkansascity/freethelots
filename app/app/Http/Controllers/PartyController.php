<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PartyController extends Controller
{
    protected function  index(){

        $party = DB::table('party')->get();

        return $party;

    }
}
