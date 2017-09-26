<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PhpParser\Node\Expr\Cast\Object_;

class PartyController extends Controller
{
    protected function  index(){

        $party = DB::table('party')->get();

        return $party;

    }

    protected function search($search){


        $name["id"] = 3;
        $name['name'] = 'Rogers';
        $name['address'] = '4101, w 80th';
        return $name;
    }
}
