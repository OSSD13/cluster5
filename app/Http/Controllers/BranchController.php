<?php

namespace App\Http\Controllers;

use App\Models\Branch_store;
use App\Models\PointOfInterest;
use App\Models\User;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return view('branch.index');
    }

    public function queryBranch(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;
        $search = $request->input('search', '');
        $userId = $request->input('user_id', session()->get('user')->user_id);

        $branchQuery = Branch_store::query();

        // ตรวจสอบสิทธิ์การเข้าถึง user_id ที่เลือก
        if ($userId) {
            $reqUserId = session()->get('user')->user_id;
            $reqUser = User::where('user_id', $reqUserId)->first();
            $reqSub = array_merge([$reqUserId], $reqUser->getSubordinateIds());

            if (!in_array($userId, $reqSub)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'คุณไม่มีสิทธิ์ดูข้อมูลสาขานี้'
                ], 403);
            }

            $user = User::where('user_id', $userId)->first();
            if ($user) {
                $subordinate = $user->getSubordinateIds();
                $targetUserIds = array_merge([$userId], $subordinate);
                $branchQuery->whereIn('branch_stores.bs_manager', $targetUserIds);
            }
        }

        // Join ตารางที่เกี่ยวข้อง
        $branchQuery->join('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id');

        // Select field ที่ต้องการ
        $branchQuery->select(
            'branch_stores.*',
            'point_of_interest_type.poit_type',
            'point_of_interest_type.poit_name',
            'point_of_interest_type.poit_icon',
            'point_of_interest_type.poit_color',
            'point_of_interest_type.poit_description',
            'users.name as bs_manager_name',
            'users.email as bs_manager_email',
            'users.user_status as bs_manager_user_status',
            'users.role_name as bs_manager_role_name'
        );

        // ค้นหาข้อมูล (search)
        if ($search) {
            $branchQuery->where(function ($query) use ($search) {
                $query->where('branch_stores.bs_name', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_type', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_name', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_icon', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_color', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_description', 'LIKE', "%$search%")
                    ->orWhere('users.name', 'LIKE', "%$search%")
                    ->orWhere('users.email', 'LIKE', "%$search%");
            });
        }

        // แยกหน้า
        $total = $branchQuery->count();
        $branch = $branchQuery->offset($offset)->limit($limit)->get();

        return response()->json([
            'data' => $branch,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'reuser' => $userId
        ]);
    }


    public function create()
    {
        return view('branch.create');
    }

    public function createBranch(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'zipcode' => 'required|numeric',
            'province' => 'required|string|max:255',
            'amphoe' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255'
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $location = \DB::table('locations')
            ->where('zipcode', $request->input('zipcode'))
            ->where('province', $request->input('province'))
            ->where('amphoe', $request->input('amphoe'))
            ->where('district', $request->input('district'))
            ->first();

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งที่ระบุ'
            ], 404);
        }

        $poi = new PointOfInterest();
        $poi->poi_name = $request->input('name');
        $poi->poi_type = 'branch';
        $poi->poi_gps_lat = $request->input('latitude');
        $poi->poi_gps_lng = $request->input('longitude');
        $poi->poi_address = $request->input('address');
        $poi->poi_location_id = $location->location_id;
        $poi->save();

        $userId = session()->get('user')->user_id;

        $branch = new Branch_store();
        $branch->bs_name = $request->input('name');
        $branch->bs_address = $request->input('address');
        $branch->bs_poi_id = $poi->id;
        $branch->bs_manager = $userId;
        $branch->bs_detail = $request->input('detail', null);
        $branch->save();

        return response()->json([
            'status' => 'success',
            'message' => 'สร้างสาขาเรียบร้อยแล้ว',
            'data' => $branch
        ]);
    }

    public function edit(Request $request)
    {
        $bs_id = $request->query('bs_id');

        $branch = \DB::table('branch_stores')
            ->join('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->join('locations', 'locations.location_id', '=', 'point_of_interests.poi_location_id')
            ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
            ->select(
                'branch_stores.*',
                'point_of_interests.poi_gps_lat',
                'point_of_interests.poi_gps_lng',
                'point_of_interests.poi_address as poi_address',
                'point_of_interest_type.poit_name',
                'point_of_interest_type.poit_type',
                'locations.zipcode',
                'locations.province',
                'locations.amphoe',
                'locations.district'
            )
            ->where('branch_stores.bs_id', $bs_id)
            ->first();

        if (!$branch) {
            return redirect()->route('branch.index')->with('error', 'ไม่พบข้อมูลสาขา');
        }

        return view('branch.edit', compact('branch'));
    }

    public function editBranch(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'bs_id' => 'required|integer|exists:branch_stores,bs_id',
            'name' => 'string|max:255',
            'address' => 'string|max:255',
            'detail' => 'nullable|string',
            'bs_manager' => 'nullable|integer|exists:users,user_id',
        ], [
            'bs_id.required' => 'กรุณาระบุรหัสสาขา',
            'bs_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'bs_id.exists' => 'ไม่พบสาขาที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $branch = Branch_store::where('bs_id', $request->input('bs_id'))->first();

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขา'
            ], 404);
        }

        if ($request->has('name'))
            $branch->bs_name = $request->input('name');
        if ($request->has('address'))
            $branch->bs_address = $request->input('address');
        if ($request->has('detail'))
            $branch->bs_detail = $request->input('detail');
        if ($request->has('bs_manager'))
            $branch->bs_manager = $request->input('bs_manager');

        $branch->save();

        return response()->json([
            'status' => 'success',
            'message' => 'แก้ไขข้อมูลสาขาเรียบร้อยแล้ว',
            'data' => $branch
        ]);
    }

    public function manage(Request $request)
    {
        $bs_id = $request->input('bs_id');

        if (!$bs_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'กรุณาระบุรหัสสาขา (bs_id)'
            ], 400);
        }

        $branch = \DB::table('branch_stores')
            ->join('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->join('locations', 'locations.location_id', '=', 'point_of_interests.poi_location_id')
            ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id')
            ->select(
                'branch_stores.*',
                'point_of_interest_type.poit_type',
                'point_of_interest_type.poit_name',
                'point_of_interest_type.poit_icon',
                'point_of_interest_type.poit_color',
                'point_of_interest_type.poit_description',
                'users.name as bs_manager_name',
                'users.email as bs_manager_email',
                'users.user_status as bs_manager_user_status',
                'users.role_name as bs_manager_role_name',
                'locations.zipcode',
                'locations.province',
                'locations.amphoe',
                'locations.district'
            )
            ->where('branch_stores.bs_id', $bs_id)
            ->first();

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขาที่ระบุ'
            ], 404);
        }

        return view('branch.manage.index', compact('branch'));
    }

    public function deleteBranch(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'bs_id' => 'required|integer|exists:branch_stores,bs_id',
        ], [
            'bs_id.required' => 'กรุณาระบุรหัสสาขา',
            'bs_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'bs_id.exists' => 'ไม่พบสาขาที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $branch = Branch_store::find($request->input('bs_id'));

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขา'
            ], 404);
        }

        // delete sale 
        $sale = \DB::table('sales')
            ->where('sales_branch_id', $branch->bs_id)
            ->delete();

        $poiId = $branch->bs_poi_id;
        $branch->delete();

        if ($poiId) {
            PointOfInterest::where('poi_id', $poiId)->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'ลบข้อมูลสาขาเรียบร้อยแล้ว'
        ]);
    }
}