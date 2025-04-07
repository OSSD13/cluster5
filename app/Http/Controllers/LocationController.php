<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LocationController extends Controller
{
    public function getLocations()
    {
        $locations = \DB::table('locations')
            ->select('district', 'amphoe', 'province', 'zipcode')
            ->get();

        return response()->json($locations);
    }
}
