<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
<<<<<<< HEAD

=======
>>>>>>> origin/develop
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchReportController extends Controller
{
<<<<<<< HEAD
    function getSubordinate()
=======
       function getSubordinate()
>>>>>>> origin/develop
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

<<<<<<< HEAD
    function getBranchReport(Request $request)
=======
    /**
     * Combines branch report and branch filtering by region or province,
     * and returns branch details with the sales data for the past 12 months.
     */
    public function getBranchReport(Request $request)
>>>>>>> origin/develop
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

<<<<<<< HEAD
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
=======
        // Build the base branch query with required joins and selected fields.
        $branchQuery = DB::table('branch_stores')
>>>>>>> origin/develop
            ->leftJoin('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->leftJoin('locations', 'point_of_interests.poi_location_id', '=', 'locations.location_id')
            ->leftJoin('users as managers', 'branch_stores.bs_manager', '=', 'managers.user_id')
            ->select(
                'branch_stores.bs_id',
                'branch_stores.bs_name',
<<<<<<< HEAD
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
        $salesData = DB::table('sales')
            ->whereIn('sales.sales_branch_id', $branches_ids)
            ->whereBetween('sales.sales_month', [
                now()->subMonths(11)->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
=======
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
                $date->copy()->subMonths(11)->startOfMonth()->format('Y-m-d'),
                $date->endOfMonth()->format('Y-m-d')
>>>>>>> origin/develop
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

<<<<<<< HEAD
        return response()->json($branches);
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
=======
        return response()->json([
            'branches' => $branches,
            'branch_count' => $branches->count(),
            'distinct_provinces' => $distinctProvinces
        ]);
    }

    public function getRegionBranch(Request $request)
    {
        $searchingUserId = $request->query('user_id') ?? session()->get('user')->user_id;
        $date = $request->query('date') ? Carbon::parse(time: $request->query('date')) : now();

        $requestUserId = session()->get('user')->user_id;
        $requestUser = User::where('user_id', '=', $requestUserId)->first();

        // check if searchingUserId is under the requestUser
        $subordinateIds = $requestUser->getSubordinateIds();
        if (!in_array($searchingUserId, $subordinateIds) && $searchingUserId != $requestUserId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // check if searchingUserId is valid
        $user = User::where('user_id', '=', $searchingUserId)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $searchingUserId = $user->user_id;
        $subordinateIds = $user->getSubordinateIds();

        if ($request->has('province')) {
            $province = $request->query('province');
            $branches = DB::table('branch_stores')
                ->leftJoin('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
                ->leftJoin('locations', 'point_of_interests.poi_location_id', '=', 'locations.location_id')
                ->leftJoin('users as managers', 'branch_stores.bs_manager', '=', 'managers.user_id')
                ->where('locations.province', $province)
                ->where(function ($query) use ($searchingUserId, $subordinateIds) {
                    $query->where('managers.user_id', '=', $searchingUserId)
                        ->orWhereIn('managers.user_id', $subordinateIds);
                })
                ->select(
                    'branch_stores.bs_id as branchId',
                    'branch_stores.bs_name as branchName',
                    'locations.province as branchProvince'
                )
                ->get();

            $currentMonth = $date->format('Y-m');
            $lastMonth = $date->copy()->subMonth()->format('Y-m');

            foreach ($branches as $branch) {
                $currentMonthSales = DB::table('sales')
                    ->where('sales.sales_branch_id', $branch->branchId)
                    ->where(DB::raw('DATE_FORMAT(sales.sales_month, "%Y-%m")'), $currentMonth)
                    ->sum('sales.sales_amount');

                $lastMonthSales = DB::table('sales')
                    ->where('sales.sales_branch_id', $branch->branchId)
                    ->where(DB::raw('DATE_FORMAT(sales.sales_month, "%Y-%m")'), $lastMonth)
                    ->sum('sales.sales_amount');

                if ($lastMonthSales > 0) {
                    $branch->branchSaleChange = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
                } else {
                    $branch->branchSaleChange = $currentMonthSales > 0 ? 100 : 0;
                }

                $branch->saleAdded = $currentMonthSales > 0;
            }

            return response()->json([
                'branches' => $branches,
                'branch_count' => $branches->count()
            ]);
        }

        if ($request->has('region')) {
            $region = $request->query('region');
            $distinctProvinces = DB::table('locations')
                ->where('region', $region)
                ->distinct()
                ->pluck('province');

            $branchCountByProvince = DB::table('branch_stores')
                ->leftJoin('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
                ->leftJoin('locations', 'point_of_interests.poi_location_id', '=', 'locations.location_id')
                ->leftJoin('users as managers', 'branch_stores.bs_manager', '=', 'managers.user_id')
                ->where('locations.region', $region)
                ->where(function ($query) use ($searchingUserId, $subordinateIds) {
                    $query->where('managers.user_id', '=', $searchingUserId)
                        ->orWhereIn('managers.user_id', $subordinateIds);
                })
                ->select('locations.province', DB::raw('COUNT(branch_stores.bs_id) as branch_count'))
                ->groupBy('locations.province')
                ->get();

            return response()->json([
                'distinct_provinces' => $distinctProvinces,
                'branch_count_by_province' => $branchCountByProvince
            ]);
        }

        $distinctRegions = DB::table('locations')
            ->distinct()
            ->pluck('region');

        $branchCountByRegion = DB::table('branch_stores')
            ->leftJoin('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->leftJoin('locations', 'point_of_interests.poi_location_id', '=', 'locations.location_id')
            ->leftJoin('users as managers', 'branch_stores.bs_manager', '=', 'managers.user_id')
            ->where(function ($query) use ($searchingUserId, $subordinateIds) {
                $query->where('managers.user_id', '=', $searchingUserId)
                    ->orWhereIn('managers.user_id', $subordinateIds);
            })
            ->select('locations.region', DB::raw('COUNT(branch_stores.bs_id) as branch_count'))
            ->groupBy('locations.region')
            ->get();

        return response()->json([
            'distinct_regions' => $distinctRegions,
            'branch_count_by_region' => $branchCountByRegion
        ]);
>>>>>>> origin/develop
    }
}
