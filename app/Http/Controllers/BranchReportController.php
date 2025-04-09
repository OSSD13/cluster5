<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchReportController extends Controller
{
    private function authorizeUser($targetUserId)
    {
        $requestUserId = session('user')->user_id;
        $requestUser = User::find($requestUserId);
        $subordinateIds = $requestUser->getSubordinateIds();

        if (!in_array($targetUserId, $subordinateIds) && $targetUserId != $requestUserId) {
            abort(403, 'Unauthorized');
        }

        return User::findOrFail($targetUserId);
    }

    public function getSubordinate()
    {
        $user = User::find(session('user')->user_id);
        $subordinates = User::whereIn('users.user_id', $user->getSubordinateIds())
            ->where('users.user_status', 'normal')
            ->leftJoin('users as managers', 'users.manager', '=', 'managers.user_id')
            ->get([
                'users.user_id', 'users.email', 'users.role_name', 'users.name',
                'managers.user_id as manager_user_id', 'managers.email as manager_email',
                'managers.role_name as manager_role_name', 'managers.name as manager_name'
            ]);

        return response()->json($subordinates);
    }

    public function getBranchReport(Request $request)
    {
        $userId = $request->query('user_id') ?? session('user')->user_id;
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : now();
        $user = $this->authorizeUser($userId);

        $branches = DB::table('branch_stores')
            ->leftJoin('point_of_interests', 'bs_poi_id', '=', 'poi_id')
            ->leftJoin('locations', 'poi_location_id', '=', 'location_id')
            ->leftJoin('users as managers', 'bs_manager', '=', 'managers.user_id')
            ->when($userId, fn($q) => $q->where(fn($query) => $query
                ->where('managers.user_id', $userId)
                ->orWhereIn('managers.user_id', $user->getSubordinateIds())))
            ->when($request->has('province'), fn($q) => $q->where('locations.province', $request->query('province')))
            ->when($request->has('region'), fn($q) => $q->where('locations.region', $request->query('region')))
            ->select('bs_id', 'bs_name', 'locations.province', 'locations.region')
            ->get();

        $salesData = DB::table('sales')
            ->whereIn('sales_branch_id', $branches->pluck('bs_id'))
            ->whereBetween('sales_month', [
                $date->copy()->subMonths(11)->startOfMonth()->format('Y-m-d'),
                $date->endOfMonth()->format('Y-m-d')
            ])
            ->select(
                'sales_branch_id',
                DB::raw('DATE_FORMAT(sales_month, "%Y-%m") as sales_month'),
                DB::raw('SUM(sales_amount) as total_sales_amount'),
                DB::raw('SUM(sales_package_amount) as total_sales_package_amount')
            )
            ->groupBy('sales_branch_id', 'sales_month')
            ->get()
            ->groupBy('sales_branch_id')
            ->map(fn($sales) => $sales->keyBy('sales_month')->map(fn($s) => [
                'sales_amount' => $s->total_sales_amount,
                'sales_package_amount' => $s->total_sales_package_amount
            ]));

        foreach ($branches as $branch) {
            $branch->monthly_sales = $salesData[$branch->bs_id] ?? [];
        }

        return response()->json([
            'branches' => $branches,
            'branch_count' => $branches->count(),
            'distinct_provinces' => $request->has('region')
                ? DB::table('locations')->where('region', $request->query('region'))->distinct()->pluck('province')
                : []
        ]);
    }

    public function getRegionBranch(Request $request)
    {
        $userId = $request->query('user_id') ?? session('user')->user_id;
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : now();
        $user = $this->authorizeUser($userId);

        $branches = DB::table('branch_stores')
            ->leftJoin('point_of_interests', 'bs_poi_id', '=', 'poi_id')
            ->leftJoin('locations', 'poi_location_id', '=', 'location_id')
            ->leftJoin('users as managers', 'bs_manager', '=', 'managers.user_id')
            ->where(fn($q) => $q
                ->where('managers.user_id', $user->user_id)
                ->orWhereIn('managers.user_id', $user->getSubordinateIds()))
            ->when($request->has('province'), fn($q) => $q->where('locations.province', $request->query('province')))
            ->when($request->has('region'), fn($q) => $q->where('locations.region', $request->query('region')))
            ->select(
                'bs_id as branchId', 'bs_name as branchName',
                'locations.province as branchProvince',
                'locations.region as branchRegion'
            )
            ->get();

        $currentMonth = $date->format('Y-m');
        $lastMonth = $date->copy()->subMonth()->format('Y-m');

        foreach ($branches as $b) {
            $curr = DB::table('sales')
                ->where('sales_branch_id', $b->branchId)
                ->where(DB::raw('DATE_FORMAT(sales_month, "%Y-%m")'), $currentMonth)
                ->sum('sales_amount');

            $prev = DB::table('sales')
                ->where('sales_branch_id', $b->branchId)
                ->where(DB::raw('DATE_FORMAT(sales_month, "%Y-%m")'), $lastMonth)
                ->sum('sales_amount');

            $b->branchSaleChange = $prev > 0 ? (($curr - $prev) / $prev) * 100 : ($curr > 0 ? 100 : 0);
            $b->saleAdded = $curr > 0;
        }

        $response = [
            'branches' => $branches,
            'branch_count' => $branches->count(),
        ];

        if ($request->has('region')) {
            $region = $request->query('region');
            $response['distinct_provinces'] = DB::table('locations')->where('region', $region)->distinct()->pluck('province');
            $response['branch_count_by_province'] = DB::table('branch_stores')
                ->leftJoin('point_of_interests', 'bs_poi_id', '=', 'poi_id')
                ->leftJoin('locations', 'poi_location_id', '=', 'location_id')
                ->leftJoin('users as managers', 'bs_manager', '=', 'managers.user_id')
                ->where('locations.region', $region)
                ->where(fn($q) => $q->where('managers.user_id', $user->user_id)->orWhereIn('managers.user_id', $user->getSubordinateIds()))
                ->select('locations.province', DB::raw('COUNT(bs_id) as branch_count'))
                ->groupBy('locations.province')
                ->get();
        } elseif (!$request->has('province')) {
            $response['distinct_regions'] = DB::table('locations')->distinct()->pluck('region');
            $response['branch_count_by_region'] = DB::table('branch_stores')
                ->leftJoin('point_of_interests', 'bs_poi_id', '=', 'poi_id')
                ->leftJoin('locations', 'poi_location_id', '=', 'location_id')
                ->leftJoin('users as managers', 'bs_manager', '=', 'managers.user_id')
                ->where(fn($q) => $q->where('managers.user_id', $user->user_id)->orWhereIn('managers.user_id', $user->getSubordinateIds()))
                ->select('locations.region', DB::raw('COUNT(bs_id) as branch_count'))
                ->groupBy('locations.region')
                ->get();
        }

        return response()->json($response);
    }
}