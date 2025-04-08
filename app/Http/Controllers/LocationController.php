<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function getLocations()
    {
        // Cache for 60 minutes (adjust as needed)
        $cacheKey = 'locations.all';
        $locations = Cache::rememberForever($cacheKey, function () {
            return DB::table('locations')
                ->select('district', 'amphoe', 'province', 'zipcode')
                ->get();
        });

        return response()->json($locations);
    }
}
