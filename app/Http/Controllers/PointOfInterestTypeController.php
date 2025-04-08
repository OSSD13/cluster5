<?php

namespace App\Http\Controllers;

use App\Models\PointOfInterestType;
use Illuminate\Http\Request;

class PointOfInterestTypeController extends Controller
{
    /*public function index(){
        return view('poi.type.index');
    }*/

    public function queryPoit(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $search = $request->input('search', '');
        $poitsQuery = PointOfInterestType::query();
        $poitsQuery->select('point_of_interest_type.*');

        if ($search) {
            $poitsQuery->where(function ($query) use ($search) {
                $query->where('point_of_interest_type.poit_type', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_name', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_icon', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_color', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_description', 'LIKE', "%$search%");
            });
        }

        $total = $poitsQuery->count();
        $poits = $poitsQuery->offset($offset)->limit($limit)->get();
        return response()->json([
            'data' => $poits,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function getPoit(Request $request)
    {
        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if (!$poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลประเภทสถานที่'
            ], 404);
        }
        return response()->json([
            'data' => $poit
        ]);
    }

    public function allPoit(Request $request)
    {
        $poits = PointOfInterestType::all();
        return response()->json([
            'data' => $poits
        ]);
    }
/*
    public function create(){
        return view('poi.type.create');
    }*/

    public function createPoit(Request $request){
        $validator = \Validator::make($request->all(), [
            'poit_type' => 'required|string|max:255',
            'poit_name' => 'required|string|max:255',
            'poit_icon' => 'required|string|max:4',
            'poit_color' => 'required|string|max:8',
            'poit_description' => 'nullable|string|max:255',
        ], [
            'poit_type.required' => 'กรุณากรอกประเภทสถานที่',
            'poit_type.string' => 'ประเภทสถานที่ต้องเป็นตัวอักษร',
            'poit_type.max' => 'ประเภทสถานที่ต้องไม่เกิน 255 ตัวอักษร',

            'poit_name.required' => 'กรุณากรอกชื่อสถานที่',
            'poit_name.string' => 'ชื่อสถานที่ต้องเป็นตัวอักษร',
            'poit_name.max' => 'ชื่อสถานที่ต้องไม่เกิน 255 ตัวอักษร',

            'poit_icon.required' => 'กรุณาเลือกไอคอน',
            'poit_icon.string' => 'ไอคอนต้องเป็นตัวอักษร',
            'poit_icon.max' => 'ไอคอนต้องไม่เกิน 4 ตัวอักษร',

            'poit_color.required' => 'กรุณากรอกรหัสสี',
            'poit_color.string' => 'รหัสสีต้องเป็นตัวอักษร',
            'poit_color.max' => 'รหัสสีต้องไม่เกิน 8 ตัวอักษร',

            'poit_description.string' => 'รายละเอียดต้องเป็นตัวอักษร',
            'poit_description.max' => 'รายละเอียดต้องไม่เกิน 255 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if ($poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'ประเภทสถานที่นี้มีอยู่แล้ว'
            ], 409);
        }

        $poit = new PointOfInterestType();
        $poit->poit_type = $request->input('poit_type');
        $poit->poit_name = $request->input('poit_name');
        $poit->poit_icon = $request->input('poit_icon');
        $poit->poit_color = $request->input('poit_color');
        $poit->poit_description = $request->input('poit_description');
        $poit->save();

        return response()->json([
            'status' => 'success',
            'message' => 'เพิ่มประเภทสถานที่เรียบร้อยแล้ว',
            'data' => $poit
        ]);
    }

    public function editPoit(Request $request){
        $validator = \Validator::make($request->all(), [
            'poit_type' => 'required|string|max:255',
            'poit_name' => 'nullable|string|max:255',
            'poit_icon' => 'nullable|string|max:4',
            'poit_color' => 'nullable|string|max:8',
            'poit_description' => 'nullable|string|max:255',
        ], [
            'poit_type.required' => 'กรุณาระบุประเภทสถานที่',
            'poit_type.string' => 'ประเภทสถานที่ต้องเป็นตัวอักษร',
            'poit_type.max' => 'ประเภทสถานที่ต้องไม่เกิน 255 ตัวอักษร',

            'poit_name.string' => 'ชื่อสถานที่ต้องเป็นตัวอักษร',
            'poit_name.max' => 'ชื่อสถานที่ต้องไม่เกิน 255 ตัวอักษร',

            'poit_icon.string' => 'ไอคอนต้องเป็นตัวอักษร',
            'poit_icon.max' => 'ไอคอนต้องไม่เกิน 4 ตัวอักษร',

            'poit_color.string' => 'รหัสสีต้องเป็นตัวอักษร',
            'poit_color.max' => 'รหัสสีต้องไม่เกิน 8 ตัวอักษร',

            'poit_description.string' => 'รายละเอียดต้องเป็นตัวอักษร',
            'poit_description.max' => 'รายละเอียดต้องไม่เกิน 255 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if (!$poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบประเภทสถานที่ที่ต้องการแก้ไข'
            ], 404);
        }

        if ($request->input('poit_name')) {
            $poit->poit_name = $request->input('poit_name');
        }
        if ($request->input('poit_icon')) {
            $poit->poit_icon = $request->input('poit_icon');
        }
        if ($request->input('poit_color')) {
            $poit->poit_color = $request->input('poit_color');
        }
        if ($request->input('poit_description')) {
            $poit->poit_description = $request->input('poit_description');
        }
        $poit->save();

        return response()->json([
            'status' => 'success',
            'message' => 'อัปเดตประเภทสถานที่เรียบร้อยแล้ว',
            'data' => $poit
        ]);
    }

    public function deletePoit(Request $request){
        $validator = \Validator::make($request->all(), [
            'poit_type' => 'required|string|max:255',
        ], [
            'poit_type.required' => 'กรุณาระบุประเภทสถานที่',
            'poit_type.string' => 'ประเภทสถานที่ต้องเป็นตัวอักษร',
            'poit_type.max' => 'ประเภทสถานที่ต้องไม่เกิน 255 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if (!$poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบประเภทสถานที่ที่ต้องการลบ'
            ], 404);
        }

        \DB::table('point_of_interests')->where('poi_type', '=', $request->input('poit_type'))->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'ลบประเภทสถานที่เรียบร้อยแล้ว'
        ]);
    }
/*
    public function editPage(){
        return view('poi.type.edit');
    }*/
}
