<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;

class EditPointOfInterestController extends Controller
{
    public function editPoiPage(Request $request)
{
    // ค้นหาข้อมูล POI จาก ID
    $show = PointOfInterest::find($request->input('id'));
    // ตรวจสอบหากไม่พบข้อมูล POI
    if (!$show) {
        return redirect()->route('poi.index')->with('error', 'ไม่พบข้อมูล POI ที่ระบุ');
    }

    // ส่งตัวแปร $show ไปยัง View
    return view('poi.edit', compact('show')); // ส่ง $show ผ่าน compact()
}


    public function editPoi(Request $request){
        
        $validator = \Validator::make($request->all(),[
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zipcode' => 'required|numeric',
            'province' => 'required|string|max:255', 
            'district' => 'required|string|max:255',
            'amphoe' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255', 
        
        ],[
            'lat.required' => 'กรุณาระบุละติจูด',
            'latnumeric' => 'ละติจูดต้องเป็นตัวเลข',
            'lng.required' => 'กรุณาระบุลองจิจูด',
            'lng.numeric' => 'ลองจิจูดต้องเป็นตัวอักษร',
            'zipcode.required' => 'กรุณาระบุรหัสไปรษณีย์',
            'zipcode.numeric' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข',
            'province.required' => 'กรุณาระบุจังหวัด',
            'province.string' => 'จังหวัดต้องเป็นตัวอักษร',
            'district.required' => 'กรุณาระบุอำเภอ',
            'district.string' => 'อำเภอต้องเป็นตัวอักษร',
            'amphoe.required' => 'กรุณาระบุตำบล',
            'amphoe.string' => 'ตำบลต้องเป็นตัวอักษร',
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
                'errors' => $validator->errors()
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
        ->where('zipcode',$request->input('zipcode'))
        ->where('province',$request->inptu('province'))
        ->where('district',$request->input('district'))
        ->where('amphoe',$request->input('amphoe'))
        ->first();
        if(!$location){
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งตรงกับที่ระบุ'
            ],404);

        }
    }
}
