<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    public function getNearbyPOIsGroupedByType(Request $request)
    {
        $latitude = (float) $request->input('lat', 13.7563);     // fallback to Bangkok lat
        $longitude = (float) $request->input('lng', 100.5018);   // fallback to Bangkok lng
        $radiusInMeters = (int) $request->input('radius', 1000);
        $limitPerType = (int) $request->input('limit', 5);
        $earthRadius = 6378137; // meters

        // Use a unique cache key based on lat/lng/radius/limit
        $cacheKey = "nearby_pois:lat:$latitude:lng:$longitude:radius:$radiusInMeters:limit:$limitPerType";

        // Cache for 5 minutes (adjust as needed)
        $results = Cache::remember($cacheKey, 60 * 5, function () use (
            $latitude, $longitude, $radiusInMeters, $limitPerType, $earthRadius
        ) {
            $subquery = DB::table('point_of_interests')
                ->join('point_of_interest_type', 'point_of_interests.poi_type', '=', 'point_of_interest_type.poit_type')
                ->select([
                    'point_of_interests.poi_id',
                    'point_of_interests.poi_name',
                    'point_of_interests.poi_type',
                    'point_of_interests.poi_gps_lat',
                    'point_of_interests.poi_gps_lng',
                    'point_of_interests.poi_address',
                    'point_of_interests.poi_location_id',
                    'point_of_interests.created_at as poi_created_at',
                    'point_of_interests.updated_at as poi_updated_at',
                    'point_of_interest_type.poit_type as poit_id',
                    'point_of_interest_type.poit_name as poit_name',
                    'point_of_interest_type.poit_icon as poit_icon',
                    'point_of_interest_type.poit_color as poit_color',
                    'point_of_interest_type.poit_description as poit_description',
                ])
                ->selectRaw("(
                    {$earthRadius} * ACOS(
                        COS(RADIANS(?)) * COS(RADIANS(poi_gps_lat)) *
                        COS(RADIANS(poi_gps_lng) - RADIANS(?)) +
                        SIN(RADIANS(?)) * SIN(RADIANS(poi_gps_lat))
                    )
                ) AS poi_distance,
                ROW_NUMBER() OVER (
                    PARTITION BY poi_type
                    ORDER BY (
                        {$earthRadius} * ACOS(
                            COS(RADIANS(?)) * COS(RADIANS(poi_gps_lat)) *
                            COS(RADIANS(poi_gps_lng) - RADIANS(?)) +
                            SIN(RADIANS(?)) * SIN(RADIANS(poi_gps_lat))
                        )
                    )
                ) AS row_num", [
                    $latitude,
                    $longitude,
                    $latitude,
                    $latitude,
                    $longitude,
                    $latitude
                ]);

            return DB::table(DB::raw("({$subquery->toSql()}) as poi_sub"))
                ->mergeBindings($subquery)
                ->where('row_num', '<=', $limitPerType)
                ->where('poi_distance', '<=', $radiusInMeters)
                ->orderBy('poi_type')
                ->orderBy('poi_distance')
                ->get();
        });

        return response()->json([
            'success' => true,
            'lat' => $latitude,
            'lng' => $longitude,
            'radius' => $radiusInMeters,
            'data' => $results
        ]);
    }
}
