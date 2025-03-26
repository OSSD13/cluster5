<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchReportController extends Controller
{
    function getSubordinate()
    {
        $requestUserId = session()->get('user')->user_id;
        $user = User::where('user_id', '=', $requestUserId)->first();
        $subordinateIds = $user->getSubordinateIds();
        $subordinates = User::whereIn('users.user_id', $subordinateIds)
            ->where('users.user_status', 'normal')
            // ->where('users.role_name', '!=', 'ceo')
            ->leftJoin('users as managers', 'users.manager', '=', 'managers.user_id')
            ->get([
                'users.user_id',
                'users.email',
                'users.role_name',
                'users.name',
                'managers.user_id as manager_user_id',
                'managers.email as manager_email',
                'managers.role_name as manager_role_name',
                'managers.name as manager_name'
            ]);

        return response()->json($subordinates);
    }

        /**
     * Combines branch report and branch filtering by region or province,
     * and returns branch details with the sales data for the past 12 months.
     */
    public function getBranchReport(Request $request)
    {
        $userId = $request->query('user_id');
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : now();
        $requestUserId = session()->get('user')->user_id;
        $requestUser = User::where('user_id', '=', $requestUserId)->first();
        $subordinateIds = $requestUser->getSubordinateIds();
        $userId = $userId ?? session()->get('user')->user_id;
        if (!in_array($userId, $subordinateIds) && $userId != $requestUserId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::where('user_id', '=', $userId)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Build the base branch query with required joins and selected fields.
        $branchQuery = DB::table('branch_stores')
            ->leftJoin('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->leftJoin('locations', 'point_of_interests.poi_location_id', '=', 'locations.location_id')
            ->leftJoin('users as managers', 'branch_stores.bs_manager', '=', 'managers.user_id')
            ->select(
            'branch_stores.bs_id',
            'branch_stores.bs_name',
            'locations.province',
            'locations.region'
            );

        // Filter by userId if provided
        if ($userId) {
            // get new subordinatesIds from the user
            $subordinateIds = $user->getSubordinateIds();
            $branchQuery->where(function ($query) use ($userId, $subordinateIds) {
                $query->where('managers.user_id', '=', $userId)
                      ->orWhereIn('managers.user_id', $subordinateIds);
            });
        }

        // Filter by month from Date if provided
        if ($date) {
            $branchQuery->whereMonth('branch_stores.created_at', '=', $date->month)
                ->whereYear('branch_stores.created_at', '=', $date->year);
        }

        $distinctProvinces = [];

        // Determine filtering method.
        if ($request->has('province')) {
            $province = $request->query('province');
            $branchQuery->where('locations.province', '=', $province);
        } elseif ($request->has('region')) {
            $region = $request->query('region');
            $branchQuery->where('locations.region', '=', $region);

            // Fetch distinct provinces in this region
            $distinctProvinces = DB::table('locations')
                ->where('region', $region)
                ->distinct()
                ->pluck('province');
        }

        // Execute the branch query.
        $branches = $branchQuery->get();
        $branches_ids = $branches->pluck('bs_id')->toArray();

        // Fetch sales data for the past 12 months.
        $salesData = DB::table('sales')
            ->whereIn('sales.sales_branch_id', $branches_ids)
            ->whereBetween('sales.sales_month', [
                now()->subMonths(11)->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ])
            ->select(
                'sales.sales_branch_id',
                DB::raw('DATE_FORMAT(sales.sales_month, "%Y-%m") as sales_month'),
                DB::raw('SUM(sales.sales_amount) as total_sales_amount'),
                DB::raw('SUM(sales.sales_package_amount) as total_sales_package_amount')
            )
            ->groupBy('sales.sales_branch_id', 'sales_month')
            ->get();

        // Transform sales data into an associative array by branch ID
        $salesByBranch = [];
        foreach ($salesData as $sale) {
            $salesByBranch[$sale->sales_branch_id][$sale->sales_month] = [
                'sales_amount' => $sale->total_sales_amount,
                'sales_package_amount' => $sale->total_sales_package_amount
            ];
        }

        // Attach sales data to branches
        foreach ($branches as $branch) {
            $branch->monthly_sales = $salesByBranch[$branch->bs_id] ?? [];
        }

        return response()->json([
            'branches' => $branches,
            'branch_count' => $branches->count(),
            'distinct_provinces' => $distinctProvinces
        ]);
    }

    function displayBs()
    {
        $user = User::where('user_id', '=', '20')->first();
        $value = json_encode(value: $user->getBranches());
        return view('displayDatabase', ['value' => $value]);
    }
    function displayTestLogin()
    {
        return view('displayTestLogin');
    }
}
