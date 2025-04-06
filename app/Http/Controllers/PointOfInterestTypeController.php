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
    public function insert(Request $request){
        $request->validate([
            'poiType' => 'required|string|max:255',
            'poiName' => 'required|string|max:255',
            'icon' => 'required',
            'color' => 'required|string|max:255',
            'poiDetails' => 'required|string|max:255',
        ],
        [
            'poiType.required' => 'กรุณากรอกข้อมูล ประเภทสถานที่',
            'poiType.string' => 'กรุณากรอกข้อมูล ประเภทสถานที่ เป็นตัวอักษร',
            'poiType.max' => 'กรุณากรอกข้อมูล ประเภทสถานที่ ไม่เกิน 255 ตัวอักษร',

            'poiName.required' => 'กรุณากรอกข้อมูล ชื่อสถานที่',
            'poiName.string' => 'กรุณากรอกข้อมูล ชื่อสถานที่ เป็นตัวอักษร',
            'poiName.max' => 'กรุณากรอกข้อมูล ชื่อสถานที่ ไม่เกิน 255 ตัวอักษร',

            'icon.required' => 'กรุณาเลือกข้อมูล ไอคอน',

            'color.required' => 'กรุณากรอกข้อมูลรหัส สี',
            'color.string' => 'กรุณากรอกข้อมูลรหัส สี เป็นตัวอักษร',
            'color.max' => 'กรุณากรอกข้อมูลรหัส สี ไม่เกิน 255 ตัวอักษร',

            'poiDetails.required' => 'กรุณากรอกข้อมูล รายละเอียดสถานที่',
            'poiDetails.string' => 'กรุณากรอกข้อมูล รายละเอียดสถานที่ เป็นตัวอักษร',
            'poiDetails.max' => 'กรุณากรอกข้อมูล รายละเอียดสถานที่ ไม่เกิน 255 ตัวอักษร',
        ]);
         return redirect()->route('poi.type.create')->with('success', 'เพิ่มประเภทสถานที่สำเร็จ');
    }
    public function edit(){
        // $poits = PointOfInterest::find($id);
        return view('poi.type.edit');
    }
}
