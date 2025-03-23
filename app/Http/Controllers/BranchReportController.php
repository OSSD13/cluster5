<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
<<<<<<< HEAD
=======

>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchReportController extends Controller
{
<<<<<<< HEAD
    /**
     * Returns the list of subordinate users.
     */
    public function getSubordinate()
    {
        $requestUserId = session()->get('user')->user_id;
        $user = User::where('user_id', $requestUserId)->first();
        $subordinateIds = $user->getSubordinateIds();

        $subordinates = User::whereIn('users.user_id', $subordinateIds)
            ->where('users.user_status', 'normal')
=======
    function getSubordinate()
    {
        $requestUserId = session()->get('user')->user_id;
        $user = User::where('user_id', '=', $requestUserId)->first();
        $subordinateIds = $user->getSubordinateIds();
        $subordinates = User::whereIn('users.user_id', $subordinateIds)
            ->where('users.user_status', 'normal')
            // ->where('users.role_name', '!=', 'ceo')
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
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

<<<<<<< HEAD
        /**
     * Combines branch report and branch filtering by region or province,
     * and returns branch details with the sales data for the past 12 months.
     */
    public function getBranchReport(Request $request)
    {
        // Build the base branch query with required joins and selected fields.
        $branchQuery = DB::table('branch_stores')
=======
    function getBranchReport(Request $request)
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

        $branches = $user->getBranches(); // return array
        $branches_ids = array_map(fn($branch) => $branch->bs_id, $branches);
        /*
            get all branches with $branches_ids
            I want
            bs_id
            bs_name
            bs_detail
            bs_address
            bs_poi_id  (
                this is a FK to point_of_interests table I want the following data
                poi_id
                poi_name
                poi_type
                poi_gps_lat
                poi_gps_lng
                poi_address
                poi_location_id (
                    this is a FK to locations table I want the following data
                    location_id
                    district
                    amphoe
                    province
                    zipcode
                    region
                    I want all of the data with prefix location_
                )
                created_at
                updated_at
                I want all of these with prefix poi_
            )

            bs_manager (
                this is a FK to users table I want the following data
                user_id
                email
                user_status
                role_name
                name
                created_at
                updated_at
                I want all of these data with prefix manager_
            )
            created_at
            updated_at

            in addition to that i want you to also join sales with the data (
                sales_id
                sales_amount
                sales_package_amount
                created_at
                updated_at
                where sales_branch_id is same
            ) only where sales_month is the same month as date
        */
        $branches = DB::table('branch_stores')
            ->whereIn('branch_stores.bs_id', $branches_ids)
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
            ->leftJoin('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->leftJoin('locations', 'point_of_interests.poi_location_id', '=', 'locations.location_id')
            ->leftJoin('users as managers', 'branch_stores.bs_manager', '=', 'managers.user_id')
            ->select(
                'branch_stores.bs_id',
                'branch_stores.bs_name',
<<<<<<< HEAD
                'locations.province',
                'locations.region'
            );

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
=======
                'branch_stores.bs_detail',
                'branch_stores.bs_address',
                'branch_stores.created_at',
                'branch_stores.updated_at',
                'point_of_interests.poi_id as poi_poi_id',
                'point_of_interests.poi_name as poi_poi_name',
                'point_of_interests.poi_type as poi_poi_type',
                'point_of_interests.poi_gps_lat as poi_poi_gps_lat',
                'point_of_interests.poi_gps_lng as poi_poi_gps_lng',
                'point_of_interests.poi_address as poi_poi_address',
                'point_of_interests.created_at as poi_created_at',
                'point_of_interests.updated_at as poi_updated_at',
                'locations.location_id as poi_location_location_id',
                'locations.district as poi_location_district',
                'locations.amphoe as poi_location_amphoe',
                'locations.province as poi_location_province',
                'locations.zipcode as poi_location_zipcode',
                'locations.region as poi_location_region',
                'managers.user_id as manager_user_id',
                'managers.email as manager_email',
                'managers.user_status as manager_user_status',
                'managers.role_name as manager_role_name',
                'managers.name as manager_name',
                'managers.created_at as manager_created_at',
                'managers.updated_at as manager_updated_at'
            )
            ->get();

        // Fetch sales data for the past 12 months
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
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

<<<<<<< HEAD
        // Transform sales data into an associative array by branch ID.
=======
        // Transform sales data into an associative array by branch ID
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
        $salesByBranch = [];
        foreach ($salesData as $sale) {
            $salesByBranch[$sale->sales_branch_id][$sale->sales_month] = [
                'sales_amount' => $sale->total_sales_amount,
                'sales_package_amount' => $sale->total_sales_package_amount
            ];
        }

<<<<<<< HEAD
        // Attach sales data to branches.
=======
        // Attach sales data to branches
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
        foreach ($branches as $branch) {
            $branch->monthly_sales = $salesByBranch[$branch->bs_id] ?? [];
        }

<<<<<<< HEAD
        return response()->json([
            'branches' => $branches,
            'branch_count' => $branches->count(),
            'distinct_provinces' => $distinctProvinces
        ]);
=======
        return response()->json($branches);
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
    }
}
