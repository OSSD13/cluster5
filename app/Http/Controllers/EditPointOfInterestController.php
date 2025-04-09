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

public function queryPoi(Request $request)
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
            'point_of_interest_type.poit_name',
            'point_of_interest_type.poit_icon',
            'point_of_interest_type.poit_color',
            'point_of_interest_type.poit_description',
            'users.name as bs_manager_name',
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
            'lat.numeric' => 'ละติจูดต้องเป็นตัวเลข',
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
        ->where('zipcode',$request->input('postal_code'))
        ->where('province',$request->inptu('province'))
        ->where('district',$request->input('district'))
        ->where('amphoe',$request->input('sub_district'))
        ->first();
        if(!$location){
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลสถานที่ตั้งตรงกับที่ระบุ'
            ],404);

        }
    }
}
