<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;

class EditPointOfInterestController extends Controller
{
    public function editPoiPage(Request $request)
{
    // ค้นหาข้อมูล POI จาก ID
    $show = PointOfInterest::find($request->input('poi_id'));
    // ตรวจสอบหากไม่พบข้อมูล POI
    if (!$show) {
        return redirect()->route('poi.index')->with('error', 'ไม่พบข้อมูล POI ที่ระบุ');
    }

    // ส่งตัวแปร $show ไปยัง View
    return view('poi.edit', compact('show')); // ส่ง $show ผ่าน compact()
}
    public function editPoi(Request $request){
        
        $validator = \Validator::make($request->all(),[
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'postal_code' => 'required|numeric',
            'province' => 'required|string|max:255', 
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', 
        
        ],[
            'latitude.required' => 'กรุณาระบุละติจูด',
            'latitude.numeric' => 'ละติจูดต้องเป็นตัวเลข',
            'longitude.required' => 'กรุณาระบุลองจิจูด',
            'longitude.numeric' => 'ลองจิจูดต้องเป็นตัวอักษร',
            'postal_code.required' => 'กรุณาระบุรหัสไปรษณีย์',
            'postal_code.numeric' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข',
            'province.required' => 'กรุณาระบุจังหวัด',
            'province.string' => 'จังหวัดต้องเป็นตัวอักษร',
            'district.required' => 'กรุณาระบุอำเภอ',
            'district.string' => 'อำเภอต้องเป็นตัวอักษร',
            'sub_district.required' => 'กรุณาระบุตำบล',
            'sub_district.string' => 'ตำบลต้องเป็นตัวอักษร',
            'address.required' => 'กรุณาระบุที่อยู่',
            'address.string' => 'ที่อยู่ต้องเป็นตัวอักษร',
            'name.required' => 'กรุณาระบุชื่อสถานที่',
            'name.string' => 'ชื่อสถานที่ต้องเป็นตัวอักษร',
            'type.required' => 'กรุณาระบุประเภทสถานที่',
            'type.string' => 'ประเภทสถานที่ต้องเป็นตัวอักษร',
        ]);

        if($validator->fail()){
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->error
            ],422);
        }
        $type = \DB::table('point_of_interest_type')->where('point_type',$request->input('type'))->first();
        if(!$type){
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบประเภทสถานที่ที่ระบุ'
            ],404);
        }
        $location = \DB::table('location')
        ->where('postal_code',$request->input('postal_code'))
        ->where('province',$request->inptu('province'))
        ->where('district',$request->input('district'))
        ->where('sub_district',$request->input('sub_district'))
        ->first();
        if(!$location){
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งตรงกับที่ระบุ'
            ],404);

        }
    }
}
