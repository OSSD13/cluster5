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
    public function edit(){
        // $poits = PointOfInterest::find($id);
        return view('poi.type.edit');
    }
}
