<?php
namespace App\Http\Controllers;

use App\Models\Branch_store;
use App\Models\PointOfInterest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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

        // If target is provided, validate access permission
        if ($target) {
            $reqUserId = auth()->user()->user_id; // Use auth() to get the authenticated user
            $reqUser = User::where('user_id', $reqUserId)->first();
            $reqSub = array_merge([$reqUserId], $reqUser->getSubordinateIds());

            if (!in_array($target, $reqSub)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'คุณไม่มีสิทธิ์ดูข้อมูลสาขานี้'
                ], 403);
            }
        }

        // Handle the query for specific user if target exists
        if ($user = User::where('user_id', $target)->first()) {
            $subordinate = $user->getSubordinateIds();
            $targetUserIds = array_merge([$target], $subordinate);
        }

        // Apply query joins and select fields
        $branchQuery->join('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id');

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

        // Search filter logic
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

        // Filter based on target user and their subordinates
        if (!empty($target) && isset($targetUserIds)) {
            $branchQuery->where(function ($query) use ($target, $targetUserIds) {
                $query->where('branch_stores.bs_manager', '=', $target)
                    ->orWhereIn('branch_stores.bs_manager', $targetUserIds);
            });
        }

        // Count total and fetch paginated data
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
        // Validate required fields
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zipcode' => 'required|numeric',
            'province' => 'required|string|max:255',
            'amphoe' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255'
        ], [
            'lat.required' => 'กรุณาระบุละติจูด',
            'lat.numeric' => 'ละติจูดต้องเป็นตัวเลขเท่านั้น',
            'lng.required' => 'กรุณาระบุลองจิจูด',
            'lng.numeric' => 'ลองจิจูดต้องเป็นตัวเลขเท่านั้น',
            'zipcode.required' => 'กรุณาระบุรหัสไปรษณีย์',
            'zipcode.numeric' => 'รหัสไปรษณีย์ต้องเป็นตัวเลขเท่านั้น',
            'province.required' => 'กรุณาระบุจังหวัด',
            'amphoe.required' => 'กรุณาระบุอำเภอ',
            'district.required' => 'กรุณาระบุตำบล',
            'address.required' => 'กรุณาระบุที่อยู่',
            'name.required' => 'กรุณาระบุชื่อสาขา',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน',
                'errors' => $validator->errors()->toArray(),
                'missing_fields' => array_keys($validator->errors()->toArray())
            ], 422);
        }

        // Validate location exists in database
        $location = DB::table('locations')
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

        try {
            // Create point of interest (POI)
            $poi = new PointOfInterest();
            $poi->poi_name = $request->input('name');
            $poi->poi_type = 'branch';
            $poi->poi_gps_lat = $request->input('lat');
            $poi->poi_gps_lng = $request->input('lng');
            $poi->poi_address = $request->input('address');
            $poi->poi_location_id = $location->location_id;
            $poi->save();

            // Use authenticated user's ID for branch manager
            $userId = auth()->user()->user_id;

            // Create branch
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
        } catch (\Exception $e) {
            // Handle any exceptions during branch creation
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการสร้างสาขา',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit()
    {
        return view('branch.edit');
    }

    public function getBranch_id(Request $request)
    {
        $branchId = $request->input('branch_id');

        if (!$branchId) {
            return response()->json([
                'status' => 'error',
                'message' => 'กรุณาระบุรหัสสาขา'
            ], 400);
        }

        $branch = Branch_store::find($branchId);

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขาที่ระบุ'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $branch
        ]);
    }
    public function editBranch(Request $request)
    {
        // Validate branch_id exists
        $branchValidator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branch_stores,id',
        ], [
            'branch_id.required' => 'กรุณาระบุรหัสสาขา',
            'branch_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'branch_id.exists' => 'ไม่พบสาขาที่ระบุ',
        ]);

        if ($branchValidator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขา',
                'errors' => $branchValidator->errors()
            ], 422);
        }

        // Then validate other fields
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'detail' => 'nullable|string',
            'bs_manager' => 'nullable|integer|exists:users,user_id',
        ], [
            'name.required' => 'กรุณาระบุชื่อสาขา',
            'address.required' => 'กรุณาระบุที่อยู่',
            'bs_manager.integer' => 'รหัสผู้จัดการต้องเป็นตัวเลข',
            'bs_manager.exists' => 'ไม่พบผู้จัดการที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'กรุณากรอกข้อมูลให้ถูกต้อง',
                'errors' => $validator->errors()->toArray(),
                'invalid_fields' => array_keys($validator->errors()->toArray())
            ], 422);
        }

        try {
            $branch = Branch_store::find($request->input('branch_id'));

            // Update fields if they are provided
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูลสาขา',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer|exists:branch_stores,id',
        ], [
            'branch_id.required' => 'กรุณาระบุรหัสสาขา',
            'branch_id.integer' => 'รหัสสาขาต้องเป็นตัวเลข',
            'branch_id.exists' => 'ไม่พบสาขาที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสาขา',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $branch = Branch_store::find($request->input('branch_id'));
            $poiId = $branch->bs_poi_id;
            $branch->delete();

            if ($poiId) {
                PointOfInterest::where('poi_id', $poiId)->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'ลบข้อมูลสาขาเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูลสาขา',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
