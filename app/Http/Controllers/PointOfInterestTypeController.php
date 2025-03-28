<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PointOfInterestTypeController extends Controller
{
    //
    public function index(){
        return view('poi.type.index');
    }
    public function create(){
        return view('poi.type.create');
    }
    public function edit(){
        // $poits = PointOfInterest::find($id);
        return view('poi.type.edit');
    }
}
