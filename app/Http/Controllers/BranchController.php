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
        $target = $request->input('target', '');

        $branchQuery = Branch_store::query();
        if ($target) {
            $reqUserId = session()->get('user')->user_id;
            $reqUser = User::where('user_id', $reqUserId)->first();
            $reqSub = array_merge([$reqUserId], $reqUser->getSubordinateIds());

            if (!in_array($target, $reqSub)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'คุณไม่มีสิทธิ์ดูข้อมูลสาขานี้'
                ], 403);
            }
        }


        $user = User::where('user_id', $target)->first();
        if ($user) {
            $subordinate = $user->getSubordinateIds();
            $targetUserIds = array_merge([$target], $subordinate);
        }

        $branchQuery->join('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id');

        $branchQuery->select(
            'branch_stores.*',
            'point_of_interest_type.poit_type',
            'point_of_interest_type.poit_name', // ประเภท
            'point_of_interest_type.poit_icon',
            'point_of_interest_type.poit_color',
            'point_of_interest_type.poit_description',
            'users.name as bs_manager_name', // เพิ่มโดย
            'users.email as bs_manager_email',
            'users.user_status as bs_manager_user_status',
            'users.role_name as bs_manager_role_name'
        );

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

        if (!empty($target) && isset($targetUserIds)) {
            $branchQuery->where(function ($query) use ($target, $targetUserIds) {
                $query->where('branch_stores.bs_manager', '=', $target)
                    ->orWhereIn('branch_stores.bs_manager', $targetUserIds);
            });
        }

        $total = $branchQuery->count();
        $branch = $branchQuery->offset($offset)->limit($limit)->get();
        return response()->json([
            'data' => $branch,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function create()
    {
        return view('branch.create');
    }

    public function createBranch(Request $request)
{
    // Validate the incoming request
    $validated = $request->validate([
        'google_link' => 'nullable|url',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'zipcode' => 'required|string',
        'province' => 'required|string',
        'amphoe' => 'required|string',
        'district' => 'required|string',
        'address' => 'required|string',
        'name' => 'required|string',
        'type' => 'nullable|string',
    ]);

    // Create the PointOfInterest (POI)
    $poi = new PointOfInterest();
    $poi->poi_name = $request->input('name');
    $poi->poi_type = $request->input('type', 'default');
    $poi->poi_gps_lat = $request->input('latitude');
    $poi->poi_gps_lng = $request->input('longitude');
    $poi->poi_address = $request->input('address');
    $poi->poi_link = $request->input('google_link');
    $poi->save();

    // Create the Branch Store
    $branch = new Branch_store();
    $branch->bs_name = $request->input('name');
    $branch->bs_address = $request->input('address');
    $branch->bs_poi_id = $poi->id;
    $branch->bs_zipcode = $request->input('zipcode');
    $branch->bs_province = $request->input('province');
    $branch->bs_amphoe = $request->input('amphoe');
    $branch->bs_district = $request->input('district');
    $branch->save();

    // Respond with the branch data after saving it
    return response()->json([
        'message' => 'บันทึกข้อมูลสำเร็จ',
        'data' => $branch
    ]);
}


    public function edit()
    {
        return view('branch.edit');
    }

    public function editBranch(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branch_stores,id',
            'name' => 'string|max:255',
            'address' => 'string|max:255',
            'detail' => 'nullable|string',
            'bs_manager' => 'nullable|integer|exists:users,user_id',
        ], [
            'branch_id.required' => 'กรุณาระบุรหัสสาขา',
            'branch_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'branch_id.exists' => 'ไม่พบสาขาที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $branch = Branch_store::find($request->input('branch_id'));

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

    public function manage()
    {
        return view('branch.manage.index');
    }

    public function deleteBranch(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branch_stores,id',
        ], [
            'branch_id.required' => 'กรุณาระบุรหัสสาขา',
            'branch_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'branch_id.exists' => 'ไม่พบสาขาที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $branch = Branch_store::find($request->input('branch_id'));

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขา'
            ], 404);
        }

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
