<?php

namespace App\Http\Controllers;

use App\Models\PointOfInterestType;
use Illuminate\Http\Request;

class PointOfInterestTypeController extends Controller
{
    //
    public function index(){
        return view('poi.type.index');
    }
    public function insert(Request $request){
        $request->validate([
            'poiType' => 'required|string|max:255',
            'poiName' => 'required|string|max:255',
            'icon' => 'required',
            'color' => 'required|string|max:255',
            'poiDetails' => 'required|string|max:255',
        ],
        [
            'poiType.required' => 'กรุณากรอกข้อมูล ประเภทสถานที่',
            'poiType.string' => 'กรุณากรอกข้อมูล ประเภทสถานที่ เป็นตัวอักษร',
            'poiType.max' => 'กรุณากรอกข้อมูล ประเภทสถานที่ ไม่เกิน 255 ตัวอักษร',

            'poiName.required' => 'กรุณากรอกข้อมูล ชื่อสถานที่',
            'poiName.string' => 'กรุณากรอกข้อมูล ชื่อสถานที่ เป็นตัวอักษร',
            'poiName.max' => 'กรุณากรอกข้อมูล ชื่อสถานที่ ไม่เกิน 255 ตัวอักษร',

            'icon.required' => 'กรุณาเลือกข้อมูล ไอคอน',

            'color.required' => 'กรุณากรอกข้อมูลรหัส สี',
            'color.string' => 'กรุณากรอกข้อมูลรหัส สี เป็นตัวอักษร',
            'color.max' => 'กรุณากรอกข้อมูลรหัส สี ไม่เกิน 255 ตัวอักษร',

            'poiDetails.required' => 'กรุณากรอกข้อมูล รายละเอียดสถานที่',
            'poiDetails.string' => 'กรุณากรอกข้อมูล รายละเอียดสถานที่ เป็นตัวอักษร',
            'poiDetails.max' => 'กรุณากรอกข้อมูล รายละเอียดสถานที่ ไม่เกิน 255 ตัวอักษร',
        ]);
         return redirect()->route('poi.type.create')->with('success', 'เพิ่มประเภทสถานที่สำเร็จ');
    }

    
    public function queryPoit(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // magic search with one search field
        $search = $request->input('search', '');
        $poitsQuery = PointOfInterestType::query();

        // select columns
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
                'message' => 'Point of interest type not found'
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
    public function create(){
        return view('poi.type.create');
    }
    public function createPoit(Request $request){
        // validate the request
        $validator = \Validator::make($request->all(), [
            'poit_type' => 'required|string|max:255',
            'poit_name' => 'required|string|max:255',
            'poit_icon' => 'required|string|max:4',
            'poit_color' => 'required|string|max:8',
            'poit_description' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
            ], 422);
        }

        // check if the point of interest type already exists
        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if ($poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Point of interest type already exists'
            ], 409);
        }

        // create the point of interest type
        $poit = new PointOfInterestType();
        $poit->poit_type = $request->input('poit_type');
        $poit->poit_name = $request->input('poit_name');
        $poit->poit_icon = $request->input('poit_icon');
        $poit->poit_color = $request->input('poit_color');
        $poit->poit_description = $request->input('poit_description');
        $poit->save();
        // return the response
        return response()->json([
            'status' => 'success',
            'message' => 'Point of interest type created successfully',
            'data' => $poit
        ]);
    }
    public function editPoit(Request $request){
        // validate the request
        $validator = \Validator::make($request->all(), [
            'poit_type' => 'required|string|max:255',
            'poit_name' => 'string|max:255',
            'poit_icon' => 'string|max:4',
            'poit_color' => 'string|max:8',
            'poit_description' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
            ], 422);
        }

        // find the point of interest type
        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if (!$poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Point of interest type not found'
            ], 404);
        }
        // update the point of interest type if specified in the request
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
        // return the response
        return response()->json([
            'status' => 'success',
            'message' => 'Point of interest type updated successfully',
            'data' => $poit
        ]);
    }
    public function deletePoit(Request $request){
        // validate the request
        $validator = \Validator::make($request->all(), [
            'poit_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
            ], 422);
        }

        // find the point of interest type
        $poit = PointOfInterestType::where('poit_type', $request->input('poit_type'))->first();
        if (!$poit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Point of interest type not found'
            ], 404);
        }
        // delete the point of interest type
        $poit->delete();
        // return the response
        return response()->json([
            'status' => 'success',
            'message' => 'Point of interest type deleted successfully'
        ]);

    }
    public function editPage(){
        // $poits = PointOfInterest::find($id);
        return view('poi.type.edit');
    }
}
