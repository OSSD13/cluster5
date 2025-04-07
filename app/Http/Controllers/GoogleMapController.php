<?php

namespace App\Http\Controllers;

use App\Services\MapService;
use Illuminate\Http\Request;

class GoogleMapController extends Controller
{
    protected $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Handle the URL conversion request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertShareToLatLng(Request $request)
    {
        $url = $request->input('url');
        if (!$url) {
            return response()->json(['error' => 'URL is required.'], 400);
        }

        $data = $this->mapService->convertLinkToLatLng($url);
        return response()->json($data);
    }
}
