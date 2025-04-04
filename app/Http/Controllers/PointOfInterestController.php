<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;  // ใช้ชื่อ Model ที่ตรงกับที่ประกาศ

class PointOfInterestController extends Controller
{
    //
    public function index()
    {
        $pois = PointOfInterest::all(); // Fetch POIs from the database
        return view('poi.index', ['pois' => $pois]);
    }
    // public function store(Request $request){
    //     $pointOfInterest = new PointOfInterest();
    //     $pointOfInterest->name = $request->name;
    //     $pointOfInterest->description = $request->description;
    //     $pointOfInterest->latitude = $request->latitude;
    //     $pointOfInterest->longitude = $request->longitude;
    //     $pointOfInterest->save();
    //     return redirect()->route('poi.index');
    // }
    public function create()
    {
        return view('poi.create');
    }
    public function edit()
    {
        // $pointOfInterest = PointOfInterest::find($id);
        return view('poi.edit');
    }
}


