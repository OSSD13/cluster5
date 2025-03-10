<?php

namespace App\Http\Controllers;

use App\Services\MapService;
use Illuminate\Http\Request;

class AnotherController extends Controller
{
    protected $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Use the MapService to convert a Google Maps URL.
     *
     * You can call this method from another controller method.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleConversion(Request $request)
    {
        // $url = $request->input('url');
        // if (!$url) {
        //     return response()->json(['error' => 'URL is required.'], 400);
        // }

        $url = "https://maps.app.goo.gl/9TbPQ4pvEPrhNdN";

        $data = $this->mapService->convertLink($url);
        return response()->json($data);
    }

    public function showMapForm() {
        return view('maps');
    }

}
