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
     * Show the conversion form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('maps'); // Make sure convert.blade.php exists in resources/views
    }

    /**
     * Handle the URL conversion request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleConversion(Request $request)
    {
        $url = $request->input('url');
        if (!$url) {
            return response()->json(['error' => 'URL is required.'], 400);
        }

        $data = $this->mapService->convertLinkToLatLng($url);
        return response()->json($data);
    }
}
