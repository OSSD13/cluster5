<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointOfInterest;  // ใช้ชื่อ Model ที่ตรงกับที่ประกาศ

class PointOfInterestController extends Controller
{
    //
    public function index()
    {
        //$pois = PointOfInterest::all(); // Fetch POIs from the database
        return view('poi.index');
    }

    public function show(Request $request)
    {

        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        // magic search with one search field and AND conditions for type and province
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $province = $request->input('province', '');

        $poisQuery = PointOfInterest::query();

        // join location table
        $poisQuery->join('locations', 'locations.location_id', '=', 'point_of_interests.poi_location_id', 'left');

        // join point_of_interest_type table
        $poisQuery->join('point_of_interest_type', 'point_of_interest_type.poit_type', '=', 'point_of_interests.poi_type', 'left');

        // select columns
        $poisQuery->select('point_of_interests.*', 'locations.*', 'point_of_interest_type.*');

        if ($search) {
            $poisQuery->where(function ($query) use ($search) {
                $query->where('point_of_interests.poi_name', 'LIKE', "%$search%")
                    ->orWhere('point_of_interest_type.poit_name', 'LIKE', "%$search%")
                    ->orWhere('locations.district', 'LIKE', "%$search%")
                    ->orWhere('locations.amphoe', 'LIKE', "%$search%")
                    ->orWhere('locations.province', 'LIKE', "%$search%")
                    ->orWhere('locations.zipcode', 'LIKE', "%$search%");
            });
        }

        if ($type) {
            $poisQuery->where('type', $type);
        }

        if ($province) {
            $poisQuery->where('province', $province);
        }

        \Illuminate\Support\Facades\DB::listen(function ($query) {
            \Log::info('SQL Query:', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });

        $total = $poisQuery->count();
        $pois = $poisQuery->offset($offset)->limit($limit)->get();
        return response()->json([
            'data' => $pois,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function create()
    {
        return view('poi.create');
    }
    public function edit(Request $request)
    {
        // $pointOfInterest = PointOfInterest::find($id);
        return view('poi.edit');
    }
}


