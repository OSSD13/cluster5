<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;  // ใช้ชื่อ Model ที่ตรงกับที่ประกาศ

class PointOfInterestController extends Controller
{
    //
    public function index()
    {
        //$pois = PointOfInterest::all(); // Fetch POIs from the database
        return view('poi.index');
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
    public function insert(Request $request){
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'postal_code' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'address'=> 'required|string|max:255',
            'name' => 'required|string|max:255',
        ],
        [
            'latitude.required' => 'กรุณากรอกข้อมูล ละติจูด',
            'latitude.numeric' => 'กรุณากรอกข้อมูล ละติจูด เป็นตัวเลข',

            'longitude.required' => 'กรุณากรอกข้อมูล ลองจิจูด',
            'longitude.numeric' => 'กรุณากรอกข้อมูล ลองจิจูด เป็นตัวเลข',

            'postal_code.required' => 'กรุณากรอกข้อมูล รหัสไปรษณีย์',
            'postal_code.string' => 'กรุณากรอกข้อมูล รหัสไปรษณีย์ เป็นตัวอักษร',
            'postal_code.max' => 'กรุณากรอกข้อมูล รหัสไปรษณีย์ ไม่เกิน 255 ตัวอักษร',

            'province.required' => 'กรุณากรอกข้อมูล จังหวัด',
            'province.string' => 'กรุณากรอกข้อมูล จังหวัด เป็นตัวอักษร',
            'province.max' => 'กรุณากรอกข้อมูล จังหวัด ไม่เกิน 255 ตัวอักษร',

            'district.string' => 'กรุณากรอกข้อมูล อำเภอ เป็นตัวอักษร',
            'district.max' => 'กรุณากรอกข้อมูล อำเภอ ไม่เกิน 255 ตัวอักษร',
            'district.required' => 'กรุณากรอกข้อมูล อำเภอ',

            'sub_district.required' => 'กรุณากรอกข้อมูล ตำบล',
            'sub_district.string' => 'กรุณากรอกข้อมูล ตำบล เป็นตัวอักษร',
            'sub_district.max' => 'กรุณากรอกข้อมูล ตำบล ไม่เกิน 255 ตัวอักษร',

            'address.string' => 'กรุณากรอกข้อมูล ที่อยู่ เป็นตัวอักษร',
            'address.max' => 'กรุณากรอกข้อมูล ที่อยู่ ไม่เกิน 255 ตัวอักษร',
            'address.required' => 'กรุณากรอกข้อมูล ที่อยู่',

            'name.string' => 'กรุณากรอกข้อมูล ชื่อสถานที่ เป็นตัวอักษร',
            'name.max' => 'กรุณากรอกข้อมูล ชื่อสถานที่ ไม่เกิน 255 ตัวอักษร',
            'name.required' => 'กรุณากรอกข้อมูล ชื่อสถานที่',
        ]);
        return redirect()->route('poi.create')->with('success', 'เพิ่มสถานที่สำเร็จ');
    }
    public function edit()
    {
        // $pointOfInterest = PointOfInterest::find($id);
        return view('poi.edit');
    }
}


