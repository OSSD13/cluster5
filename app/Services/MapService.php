<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapService
{
    protected $apiKey;

    public function __construct()
    {
        // Load your Google Maps API key from the environment.
        $this->apiKey = env('GOOGLE_MAPS_API_KEY');
    }

    /**
     * Resolve a shortened Google Maps URL (e.g. maps.app.goo.gl/...) to its final destination.
     * Then, remove extra query parameters that are not needed.
     *
     * @param string $url
     * @return string
     */
    private function resolveShortUrl(string $url): string
    {
        $ch = curl_init($url);
        // Allow cURL to follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // We don't need the content; just want the final URL.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $finalUrl;
    }

    /**
     * Convert a Google Maps URL into detailed place data.
     *
     * @param string $url
     * @return array
     */
    public function convertLink(string $url): array
    {
        Log::info('Converting Google Maps URL', ['url' => $url]);
        // If the URL is a shortened Google Maps URL, resolve it.
        if (strpos($url, 'maps.app.goo.gl') !== false) {
            Log::info('Resolving shortened URL', ['url' => $url]);
            $url = $this->resolveShortUrl($url);
        }

        Log::info('Resolved URL', ['resolved_url' => $url]);
        // https://www.google.com/maps?q=%E0%B8%A1.%E0%B8%9A%E0%B8%B9%E0%B8%A3%E0%B8%9E%E0%B8%B2+86/24+%E0%B8%8B%E0%B8%AD%E0%B8%A2+%E0%B8%81%E0%B9%88%E0%B8%87%E0%B8%A2%E0%B8%B4%E0%B9%89%E0%B8%A1+%E0%B8%95%E0%B8%B3%E0%B8%9A%E0%B8%A5%E0%B9%81%E0%B8%AA%E0%B8%99%E0%B8%AA%E0%B8%B8%E0%B8%82+%E0%B8%AD%E0%B8%B3%E0%B9%80%E0%B8%A0%E0%B8%AD%E0%B9%80%E0%B8%A1%E0%B8%B7%E0%B8%AD%E0%B8%87%E0%B8%8A%E0%B8%A5%E0%B8%9A%E0%B8%B8%E0%B8%A3%E0%B8%B5+%E0%B8%8A%E0%B8%A5%E0%B8%9A%E0%B8%B8%E0%B8%A3%E0%B8%B5+20130&ftid=0x3102b50016490b1d:0xe0668a8bf7938624&entry=gps&lucs=,94255448,94242604,94224825,94227247,94227248,94231188,47071704,47069508,94218641,94203019,47084304,94208458,94208447&g_ep=CAISEjI1LjA5LjEuNzMwNTIxODM1MBgAINeCAyp1LDk0MjU1NDQ4LDk0MjQyNjA0LDk0MjI0ODI1LDk0MjI3MjQ3LDk0MjI3MjQ4LDk0MjMxMTg4LDQ3MDcxNzA0LDQ3MDY5NTA4LDk0MjE4NjQxLDk0MjAzMDE5LDQ3MDg0MzA0LDk0MjA4NDU4LDk0MjA4NDQ3QgJUSA%3D%3D&skid=bff0fa5a-9c34-4f6b-a7cd-45a508859961&g_st=com.google.maps.preview.copy
        // Try to extract a place name first.


        // If no place name was found, try to extract coordinates.
        $coords = $this->extractCoordinates($url);
        Log::info('Extracted coordinates', ['coords' => $coords]);
        if ($coords) {
            return $this->reverseGeocode($coords['lat'], $coords['lng']);
        }

        $placeName = $this->extractPlaceName($url);
        Log::info('Extracted place name', ['place_name' => $placeName]);
        if ($placeName) {
            $canonicalPlaceId = $this->getCanonicalPlaceId($placeName);
            if ($canonicalPlaceId) {
                return $this->getPlaceDetails($canonicalPlaceId);
            }
            return ['error' => 'Could not retrieve canonical Place ID.'];
        }

        return ['error' => 'Could not extract data from the provided URL.'];
    }
    
    /**
     * Extract the place name from a Google Maps URL.
     * Supports both '/maps/place/' and '?q=' formats.
     *
     * @param string $url
     * @return string|null
     */
    private function extractPlaceName(string $url): ?string
    {
        $parsedUrl = parse_url($url);

        // Check if the path contains "/place/"
        if (isset($parsedUrl['path'])) {
            $parts = explode('/', $parsedUrl['path']);
            if (count($parts) >= 4 && $parts[2] === 'place') {
                return urldecode($parts[3]);
            }
        }

        // Check if the query string contains "q="
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            if (!empty($queryParams['q'])) {
                return urldecode($queryParams['q']);
            }
        }

        return null;
    }


    /**
     * Extract coordinates from a Google Maps URL.
     * Expected URL format: contains '@<lat>,<lng>'
     *
     * @param string $url
     * @return array|null
     */
    private function extractCoordinates(string $url): ?array
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['path'])) {
            return null;
        }

        // Try to extract coordinates from URLs containing '/place/<lat>,<lng>'
        if (preg_match('/\/place\/(-?\d+\.\d+),(-?\d+\.\d+)/', $parsedUrl['path'], $matches)) {
            return [
                'lat' => floatval($matches[1]),
                'lng' => floatval($matches[2]),
            ];
        }

        return null;
    }

    /**
     * Use the Find Place API to convert a place name to a canonical Place ID.
     *
     * @param string $inputText
     * @return string|null
     */
    private function getCanonicalPlaceId(string $inputText): ?string
    {
        $url = 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json';
        $response = Http::get($url, [
            'input' => $inputText,
            'inputtype' => 'textquery',
            'fields' => 'place_id',
            'key' => $this->apiKey,
        ]);

        $data = $response->json();
        if (isset($data['status']) && $data['status'] === 'OK' && !empty($data['candidates'])) {
            return $data['candidates'][0]['place_id'];
        }
        return null;
    }

    /**
     * Retrieve detailed place data using the Place Details API.
     *
     * @param string $placeId
     * @return array
     */
    private function getPlaceDetails(string $placeId): array
    {
        $url = 'https://maps.googleapis.com/maps/api/place/details/json';
        $response = Http::get($url, [
            'placeid' => $placeId,
            'key' => $this->apiKey,
        ]);

        return $response->json();
    }

    /**
     * Reverse geocode coordinates using the Geocoding API.
     *
     * @param float $lat
     * @param float $lng
     * @return array
     */
    private function reverseGeocode(float $lat, float $lng): array
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = Http::get($url, [
            'latlng' => "{$lat},{$lng}",
            'key' => $this->apiKey,
        ]);

        return $response->json();
    }
}
