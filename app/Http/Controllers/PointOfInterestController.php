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

        // magic search with one search field and AND conditions for type and province
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $province = $request->input('province', '');

        $poisQuery = PointOfInterest::query();

        // join location table
        $poisQuery->join('locations', 'locations.location_id', '=', 'point_of_interests.poi_location_id', 'left');

        // join point_of_interest_type table
        $poisQuery->join('point_of_interest_type', 'point_of_interest_type.poit_type', '=', 'point_of_interests.poi_type', 'left');

        // select columns
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
            $poisQuery->where('type', $type);
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
    {
        return view('poi.create');
    }
    public function createPoi(Request $request)
    {
        // lat lng zipcode province amphoe district address name type
        // validate request
        $validator = \Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zipcode' => 'required|numeric',
            'province' => 'required|string|max:255',
            'amphoe' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
            ], 422);
        }

        // find type
        $type = \DB::table('point_of_interest_type')->where('poit_type', $request->input(key: 'type'))->first();
        if (!$type) {
            return response()->json([
                'status' => 'error',
                'message' => 'Type not found'
            ], 404);
        }


        // find location id
        $location = \DB::table('locations')->where('zipcode', $request->input('zipcode'))
            ->where('province', $request->input('province'))
            ->where('amphoe', $request->input('amphoe'))
            ->where('district', $request->input('district'))
            ->first();
        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Location not found'
            ], 404);
        }
        // create point of interest
        $poi = new PointOfInterest();
        $poi->poi_name = $request->input('name');
        $poi->poi_type = $type->poit_type;
        $poi->poi_gps_lat = $request->input('lat');
        $poi->poi_gps_lng = $request->input('lng');
        $poi->poi_address = $request->input('address');
        $poi->poi_location_id = $location->location_id;
        $poi->save();

        // return response
        return response()->json([
            'status' => 'success',
            'message' => 'Point of interest created successfully',
            'data' => $poi
        ]);
    }
    public function editPage(Request $request)
    {
        // $pointOfInterest = PointOfInterest::find($id);
        return view('poi.edit');
    }
    public function editPoi(Request $request)
    {
        return view('poi.create');
    }
}


