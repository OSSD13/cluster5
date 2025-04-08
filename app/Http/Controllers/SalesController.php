<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\User;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function querySales(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $branchId = $request->input('bs_id');
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $salesQuery = DB::table('sales')
            ->join('branch_stores', 'sales.sales_branch_id', '=', 'branch_stores.bs_id')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id')
            ->select(
                'sales.sales_id',
                'sales.sales_branch_id',
                'branch_stores.bs_name as branch_name',
                'sales.sales_month',
                'sales.sales_amount',
                'sales.sales_package_amount',
                'users.name as manager_name'
            );

        if ($branchId) {
            $salesQuery->where('sales.sales_branch_id', $branchId);
        }

        if ($userId) {
            $salesQuery->where('users.user_id', $userId);
        }

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('sales.sales_month', [$startDate, $endDate]);
        }

        $total = $salesQuery->count();
        $sales = $salesQuery->offset($offset)->limit($limit)->get();

        return response()->json([
            'data' => $sales,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }
    public function editSales(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'sales_id' => 'required|integer|exists:sales,sales_id',
            'sales_amount' => 'nullable|numeric|min:0',
            'sales_package_amount' => 'nullable|numeric|min:0',
            'sales_month' => 'nullable|date_format:Y-m-d',
        ], [
            'sales_id.required' => 'กรุณาระบุรหัสการขาย',
            'sales_id.integer' => 'รหัสการขายต้องเป็นตัวเลข',
            'sales_id.exists' => 'ไม่พบข้อมูลการขายที่ระบุ',
            'sales_amount.numeric' => 'ยอดขายต้องเป็นตัวเลข',
            'sales_package_amount.numeric' => 'ยอดแพ็คเกจต้องเป็นตัวเลข',
            'sales_month.date_format' => 'รูปแบบวันที่ต้องเป็น YYYY-MM-DD',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $sales = DB::table('sales')->where('sales_id', $request->input('sales_id'))->first();

        if (!$sales) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลการขาย'
            ], 404);
        }

        $updateData = [];
        if ($request->has('sales_amount')) {
            $updateData['sales_amount'] = $request->input('sales_amount');
        }
        if ($request->has('sales_package_amount')) {
            $updateData['sales_package_amount'] = $request->input('sales_package_amount');
        }
        if ($request->has('sales_month')) {
            $updateData['sales_month'] = $request->input('sales_month');
        }

        DB::table('sales')->where('sales_id', $request->input('sales_id'))->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'แก้ไขข้อมูลการขายเรียบร้อยแล้ว',
            'data' => $updateData
        ]);
    }
    public function deleteSales(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'sales_id' => 'required|integer|exists:sales,sales_id',
        ], [
            'sales_id.required' => 'กรุณาระบุรหัสการขาย',
            'sales_id.integer' => 'รหัสการขายต้องเป็นตัวเลข',
            'sales_id.exists' => 'ไม่พบข้อมูลการขายที่ระบุ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'การตรวจสอบข้อมูลล้มเหลว',
                'errors' => $validator->errors()
            ], 422);
        }

        $sales = DB::table('sales')->where('sales_id', $request->input('sales_id'))->first();

        if (!$sales) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลการขาย'
            ], 404);
        }

        DB::table('sales')->where('sales_id', $request->input('sales_id'))->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ลบข้อมูลการขายเรียบร้อยแล้ว'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'status' => 'error',
                'message' => 'กรุณาระบุคำค้นหา'
            ], 400);
        }

        $results = DB::table('sales')
            ->join('branch_stores', 'sales.sales_branch_id', '=', 'branch_stores.bs_id')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id')
            ->select(
                'sales.sales_id',
                'sales.sales_branch_id',
                'branch_stores.bs_name as branch_name',
                'sales.sales_month',
                'sales.sales_amount',
                'sales.sales_package_amount',
                'users.name as manager_name'
            )
            ->where('branch_stores.bs_name', 'like', "%{$query}%")
            ->orWhere('users.name', 'like', "%{$query}%")
            ->orWhere('sales.sales_month', 'like', "%{$query}%")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }


}