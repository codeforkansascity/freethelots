<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use DB;

class ParcelController extends Controller
{
    protected function index(){

        $parcels = DB::table('parcel')->get();

        return $parcels;

    }
}
