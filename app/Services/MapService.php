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
     * Convert a Google Maps URL into detailed place data with lat/lng.
     *
     * Process:
     * 1. If the URL is shortened, resolve it.
     * 2. Try to extract coordinates directly from the URL.
     * 3. If coordinates exist, return them immediately.
     * 4. Otherwise, extract the place name (from the 'q' parameter) and use the Geocoding API.
     *
     * @param string $url
     * @return array
     */
    public function convertLinkToLatLng(string $url): array
    {
        Log::info('Converting Google Maps URL', ['url' => $url]);

        // Resolve shortened URLs
        if (strpos($url, 'maps.app.goo.gl') !== false) {
            Log::info('Resolving shortened URL', ['url' => $url]);
            $url = $this->resolveShortUrl($url);
        }

        Log::info('Resolved URL', ['resolved_url' => $url]);

        // Try to extract coordinates directly from the URL.
        $coords = $this->extractCoordinates($url);
        if ($coords) {
            Log::info('Coordinates extracted from URL', ['coords' => $coords]);
            return $coords;
        }

        // If no coordinates found, extract the place name from the URL.
        $address = $this->extractPlaceName($url);
        if (!$address) {
            Log::error('No address found in URL');
            return ['error' => 'Could not extract data from the provided URL.'];
        }
        Log::info('Place name extracted from URL', ['address' => $address]);

        // Use the Geocoding API to get coordinates for the address.
        $coords = $this->getLatLngFromAddress($address);
        if ($coords) {
            Log::info('Coordinates obtained via Geocoding API', ['coords' => $coords]);
            return $coords;
        }

        return ['error' => 'Could not extract data from the provided URL or address.'];
    }

    /**
     * Extract coordinates from a Google Maps URL.
     * Supports patterns: '!3d<lat>!4d<lng>', '@<lat>,<lng>', and '/search/<lat>,<lng>'
     *
     * @param string $url
     * @return array|null
     */
    private function extractCoordinates(string $url): ?array
    {
        // Pattern 1: !3d<lat>!4d<lng>
        if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $matches)) {
            return [
                'lat' => floatval($matches[1]),
                'lng' => floatval($matches[2]),
            ];
        }

        // Pattern 2: @<lat>,<lng>
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            return [
                'lat' => floatval($matches[1]),
                'lng' => floatval($matches[2]),
            ];
        }

        // Pattern 3: /search/<lat>,<lng>
        if (preg_match('/\/search\/((-|\+)?\d+\.\d+),((-|\+)?\d+\.\d+)/', $url, $matches)) {
            return [
                'lat' => floatval($matches[1]),
                'lng' => floatval($matches[3]),
            ];
        }

        // No coordinates found.
        return null;
    }

    /**
     * Extract the place name from the Google Maps URL.
     * Supports both URLs with a 'q' query parameter and URLs with a '/maps/place/<place>' path.
     *
     * @param string $url
     * @return string|null
     */
    private function extractPlaceName(string $url): ?string
    {
        $parts = parse_url($url);

        // Check if the URL path contains '/maps/place/'
        if (isset($parts['path'])) {
            // Use regex to capture everything after '/maps/place/' up to the next slash
            if (preg_match('#/maps/place/([^/]+)#', $parts['path'], $matches)) {
                // The extracted segment is URL-encoded; decode it to get the human-readable address.
                $place = urldecode($matches[1]);

                // Optionally, further clean the place name if needed (e.g., remove extra characters).
                return $place;
            }
        }

        // Fallback: check for a 'q' query parameter if the above did not match.
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['q'])) {
                return urldecode($query['q']);
            }
        }

        return null;
    }

    /**
     * Get latitude and longitude from an address using Google's Geocoding API.
     *
     * @param string $address
     * @return array|null
     */
    private function getLatLngFromAddress(string $address): ?array
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = Http::get($url, [
            'address' => $address,
            'key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'lat' => $location['lat'],
                    'lng' => $location['lng'],
                ];
            }
        }

        return null;
    }
}
