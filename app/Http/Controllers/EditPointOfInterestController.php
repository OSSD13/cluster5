<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;

class EditPointOfInterestController extends Controller
{
    public function editPoiPage(Request $request)
    {
        // ค้นหาข้อมูล POI จาก ID
        $poi = PointOfInterest::find($request->input('poi_id'));
        // ตรวจสอบหากไม่พบข้อมูล POI
        if (!$poi) {
            return redirect()->route('poi.index')->with('error', 'ไม่พบข้อมูล POI ที่ระบุ');
        }

        $locations = null;
        if ($poi->poi_location_id) {
            $locations = \DB::table('locations')
                ->where('location_id', $poi->poi_location_id)
                ->first();
        }

        $poiTypes = \DB::table('point_of_interest_type')->get();


            // ส่งตัวแปร $show ไปยัง View
            return view('poi.edit', compact('poi', 'poiTypes', 'locations')); // ส่ง $show ผ่าน compact()
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
    public function editPoi(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric', // fixed field name
        'zipcode' => 'nullable|numeric',
        'province' => 'nullable|string|max:255',
        'district' => 'nullable|string|max:255',
        'amphoe' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:255', 
    ], [
        'latitude.required' => 'กรุณาระบุละติจูด',
        'latitude.numeric' => 'ละติจูดต้องเป็นตัวเลข',
        'longitude.required' => 'กรุณาระบุลองจิจูด',
        'longitude.numeric' => 'ลองจิจูดต้องเป็นตัวเลข',
        'zipcode.numeric' => 'รหัสไปรษณีย์ต้องเป็นตัวเลข',
        'province.string' => 'จังหวัดต้องเป็นตัวอักษร',
        'district.string' => 'อำเภอต้องเป็นตัวอักษร',
        'amphoe.string' => 'ตำบลต้องเป็นตัวอักษร',
        'address.string' => 'ที่อยู่ต้องเป็นตัวอักษร',
        'name.required' => 'กรุณาระบุชื่อสถานที่',
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

    $type = \DB::table('point_of_interest_type')
        ->where('poit_type', $request->input('type'))
        ->first();

    if (!$type) {
        return response()->json([
            'status' => 'error',
            'message' => 'ไม่พบประเภทสถานที่ที่ระบุ'
        ], 404);
    }

    // Optional: only check location if all fields exist
    $hasLocationFields = $request->filled(['zipcode', 'province', 'district', 'amphoe']);
    if ($hasLocationFields) {
        $location = \DB::table('location')
            ->where('zipcode', $request->input('zipcode'))
            ->where('province', $request->input('province'))
            ->where('district', $request->input('district'))
            ->where('amphoe', $request->input('amphoe'))
            ->first();

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งตรงกับที่ระบุ'
            ], 404);
        }
    }

    // ✅ Update the POI
    $poi = PointOfInterest::find($request->input('poi_id'));
    if (!$poi) {
        return response()->json([
            'status' => 'error',
            'message' => 'ไม่พบข้อมูลสถานที่ที่ระบุ'
        ], 404);
    }
    $poi->poi_name = $request->input('name');
    $poi->poi_location_id = $location->location_id ?? null; // Use location ID if exists
    $poi->poi_type = $type->poit_type;
    $poi->poi_gps_lat = $request->input('latitude');
    $poi->poi_gps_lng = $request->input('longitude');
    $poi->poi_address = $request->input('address');
    $poi->save();

    return response()->json([
        'status' => 'success',
        'message' => 'อัปเดตข้อมูลสถานที่เรียบร้อยแล้ว'
    ]);
}

}
