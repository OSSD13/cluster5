<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;

class CreatePointOfInterestController extends Controller
{
    public function createPoi(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'postal_code' => 'required|numeric|digits:5',
            'province' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ], [
            'latitude.required' => 'กรุณาระบุละติจูด',
            'latitude.numeric' => 'ละติจูดต้องเป็นตัวเลข',
            'longitude.required' => 'กรุณาระบุลองจิจูด',
            'longitude.numeric' => 'ลองจิจูดต้องเป็นตัวเลข',
            'postal_code.required' => 'กรุณาระบุรหัสไปรษณีย์',
            'postal_code.numeric' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข',
            'province.required' => 'กรุณาระบุจังหวัด',
            'province.string' => 'จังหวัดต้องเป็นตัวอักษร',
            'sub_district.required' => 'กรุณาระบุอำเภอ',
            'sub_district.string' => 'อำเภอต้องเป็นตัวอักษร',
            'district.required' => 'กรุณาระบุตำบล',
            'district.string' => 'ตำบลต้องเป็นตัวอักษร',
            'address.required' => 'กรุณาระบุที่อยู่',
            'address.string' => 'ที่อยู่ต้องเป็นตัวอักษร',
            'name.required' => 'กรุณาระชื่อสถานที่',
            'name.string' => 'ชื่อสถานที่ต้องเป็นตัวอักษร',
            'type.required' => 'กรุณาระบุประเภทสถานที่',
            'type.string' => 'ประเภทสถานที่ต้องเป็นตัวอักษร',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $type = \DB::table('point_of_interest_type')->where('poit_type', $request->input('type'))->first();
        if (!$type) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบประเภทสถานที่ที่ระบุ'
            ], 404);
        }

        $location = \DB::table('locations')
            ->where('zipcode', $request->input('zipcode'))
            ->where('province', $request->input('province'))
            ->where('sub_district', $request->input('sub_district'))
            ->where('district', $request->input('district'))
            ->first();

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งที่ตรงกับที่ระบุ'
            ], 404);
        }

        $poi = new PointOfInterest();
        $poi->poi_name = $request->input('name');
        $poi->poi_type = $type->poit_type;
        $poi->poi_gps_lat = $request->input('lattitude');
        $poi->poi_gps_lng = $request->input('longitude');
        $poi->poi_address = $request->input('address');
        $poi->poi_location_id = $location->location_id;
        $poi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'เพิ่มสถานที่เรียบร้อยแล้ว',
            'data' => $poi
        ]);
    }
}
