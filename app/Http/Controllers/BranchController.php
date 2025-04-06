<?php

namespace App\Http\Controllers;

use App\Models\Branch_store;
use App\Models\PointOfInterest;
use App\Models\User;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    public function index(){
        return view('branch.index');
    }
    public function queryBranch(Request $request){
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // magic search with one search field and AND conditions for type and province
        $search = $request->input('search', '');

        $branchQuery = Branch_store::query();

        $target = $request->input('target', '');
        // check if target is valid user id
        $user = User::where('user_id', $target)->first();
        if ($user) {
            $subordinate = $user->getSubordinateIds();
            $targetUserIds = array_merge([$target], $subordinate);
        }


        // join bs_poi_id then poi_type and poi_location_id then join bs_manager

        $branchQuery->join('point_of_interests', 'branch_stores.bs_poi_id', '=', 'point_of_interests.poi_id')
            ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
            ->join('users', 'branch_stores.bs_manager', '=', 'users.user_id'); // as bs_manager

        // select columns
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

    public function createBranch(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zipcode' => 'required|numeric',
            'province' => 'required|string|max:255',
            'amphoe' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
            ], 422);
        }

        // check if location is valid
        $location = \DB::table('locations')
            ->where('zipcode', $request->input('zipcode'))
            ->where('province', $request->input('province'))
            ->where('amphoe', $request->input('amphoe'))
            ->where('district', $request->input('district'))
            ->first();

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Location not found'
            ], 404);
        }

        // create new poi
        $poi = new PointOfInterest();
        $poi->poi_name = $request->input('name');
        $poi->poi_type = 'branch';
        $poi->poi_gps_lat = $request->input('lat');
        $poi->poi_gps_lng = $request->input('lng');
        $poi->poi_address = $request->input('address');
        $poi->poi_location_id = $location->location_id;
        $poi->save();

        $poiId = $poi->id; // Use the newly created POI ID

        // get current user id
        $userId = session()->get('user')->user_id;

        // create new branch
        $branch = new Branch_store();
        $branch->bs_name = $request->input('name');
        $branch->bs_address = $request->input('address');
        $branch->bs_poi_id = $poiId; // Use the POI ID from the previous step
        $branch->bs_manager = $userId; // Assuming you have a manager_id in the request
        $branch->bs_detail = $request->input('detail', null); // Optional field
        $branch->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Branch created successfully',
            'data' => $branch
        ]);
    }
    public function create(){
        return view('branch.create');
    }
    public function edit(){
        return view('branch.edit');
    }
    public function manage(){
        return view('branch.manage.index');
    }


}
