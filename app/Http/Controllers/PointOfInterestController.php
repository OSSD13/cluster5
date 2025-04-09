<?php

namespace App\Http\Controllers;

use App\Models\PointOfInterestType;
use Illuminate\Http\Request;
use App\Models\PointOfInterest;

class PointOfInterestController extends Controller
{
    public function index()
    {
        $poits = PointOfInterestType::all();
        $provinces = \DB::table("locations")
            ->select('province')
            ->distinct()
            ->get();
        return view('poi.index', compact('poits','provinces'));
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
            'type' => 'required',
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

            'type.required' => 'กรุณาเลือกข้อมูล ประเภทสถานที่',
        ]);
        return redirect()->route('poi.create')->with('success', 'เพิ่มสถานที่สำเร็จ');
    }

    public function queryPoi(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $province = $request->input('province', '');

        $poisQuery = PointOfInterest::query();

        $poisQuery->join('locations', 'locations.location_id', '=', 'point_of_interests.poi_location_id', 'left');
        $poisQuery->join('point_of_interest_type', 'point_of_interest_type.poit_type', '=', 'point_of_interests.poi_type', 'left');

        $poisQuery->select('point_of_interests.*', 'locations.*', 'point_of_interest_type.*');

        if ($search) {
            $poisQuery->where(function ($query) use ($search) {
                $query->where('point_of_interests.poi_name', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_name', 'LIKE', "%$search%")
                    ->orWhere('locations.district', 'LIKE', "%$search%")
                    ->orWhere('locations.amphoe', 'LIKE', "%$search%")
                    ->orWhere('locations.province', 'LIKE', "%$search%")
                    ->orWhere('locations.zipcode', 'LIKE', "%$search%");
            });
        }

        if ($type) {
            $poisQuery->where('point_of_interests.poi_type', '=', $type);
        }

        if ($province) {
            $poisQuery->where('province', $province);
        }

        \Illuminate\Support\Facades\DB::listen(function ($query) {
            \Log::info('SQL Query:', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });

        $total = $poisQuery->count();
        $pois = $poisQuery->offset($offset)->limit($limit)->get();
        return response()->json([
            'data' => $pois,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function createPage()
    {        // get all poi type
        $poiTypes = \DB::table('point_of_interest_type')
            ->select('poit_type', 'poit_name', 'poit_icon')
            ->get();
        return view('poi.create', compact('poiTypes'));
    }

    public function createPoi(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'zipcode' => 'required|numeric|digits:5',
            'province' => 'required|string|max:255',
            'amphoe' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ], [
            'latitude.required' => 'กรุณาระบุละติจูด',
            'latitude.numeric' => 'ละติจูดต้องเป็นตัวเลข',
            'longitude.required' => 'กรุณาระบุลองจิจูด',
            'longitude.numeric' => 'ลองจิจูดต้องเป็นตัวเลข',
            'zipcode.required' => 'กรุณาระบุรหัสไปรษณีย์',
            'zipcode.numeric' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข',
            'province.required' => 'กรุณาระบุจังหวัด',
            'province.string' => 'จังหวัดต้องเป็นตัวอักษร',
            'amphoe.required' => 'กรุณาระบุอำเภอ',
            'amphoe.string' => 'อำเภอต้องเป็นตัวอักษร',
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
            ->where('zipcode', '=', $request->input('zipcode'))
            ->where('province', '=', $request->input('province'))
            ->where('amphoe', '=', $request->input('amphoe'))
            ->where('district', '=', $request->input('district'))
            ->first();

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งตรงกับที่ระบุ'
            ], 404);
        }

        $poi = new PointOfInterest();
        $poi->poi_name = $request->input('name');
        $poi->poi_type = $type->poit_type;
        $poi->poi_gps_lat = $request->input('latitude');
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

    public function editPage(Request $request)
    {
        return view('poi.edit');
    }

    public function editPoi(Request $request)
    {
        return view('poi.create');
    }
    
    public function deletePoi(Request $request){
        $validator = \Validator::make($request->all(), [
            'poi_id' => 'required|numeric|exists:point_of_interests,poi_id',
        ], [
            'poi_id.required' => 'กรุณาระบุรหัสผู้ใช้งาน',
            'poi_id.numeric' => 'รหัสผู้ใช้งานต้องเป็นตัวเลข',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $poi = PointOfInterest::where('poi_id', '=', $request->input('poi_id'))->first();
        if (!$poi) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบประเภทสถานที่ที่ต้องการลบ'
            ], 404);
        }

        \DB::statement('DELETE FROM point_of_interests WHERE poi_id = ?', bindings: [$request->input('poi_id')]);
        return response()->json([
            'status' => 'success',
            'message' => 'ลบประเภทสถานที่เรียบร้อยแล้ว'
        ]);
    }
}