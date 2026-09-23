<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodeController extends Controller
{
    /**
     * Reverse Geocoding Proxy (lat, lng -> Address Components)
     * Menggunakan cache 24 jam untuk koordinat yang mirip guna menghemat request
     */
    public function reverse(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!$lat || !$lng || !is_numeric($lat) || !is_numeric($lng)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter lat dan lng harus berupa angka valid.',
            ], 422);
        }

        $lat = round((float) $lat, 5);
        $lng = round((float) $lng, 5);
        $cacheKey = "geocode_{$lat}_{$lng}";

        $result = Cache::remember($cacheKey, 86400, function () use ($lat, $lng) {
            try {
                // Request ke Nominatim OpenStreetMap
                $response = Http::withHeaders([
                    'User-Agent' => 'MSN-WorkReport-App/1.0 (internal-ticketing)',
                ])->timeout(6)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format'          => 'jsonv2',
                    'lat'             => $lat,
                    'lon'             => $lng,
                    'zoom'            => 18,
                    'addressdetails'  => 1,
                    'accept-language' => 'id',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $addr = $data['address'] ?? [];

                    $road = $addr['road'] ?? $addr['pedestrian'] ?? $addr['street'] ?? $addr['residential'] ?? '';
                    $houseNumber = $addr['house_number'] ?? '';
                    $roadLine = trim(($houseNumber ? "No {$houseNumber} " : "") . $road);

                    $village = $addr['village'] ?? $addr['suburb'] ?? $addr['neighbourhood'] ?? $addr['quarter'] ?? '';
                    $district = $addr['city_district'] ?? $addr['subdistrict'] ?? $addr['municipality'] ?? $addr['county'] ?? '';
                    if ($district && !preg_match('/^(kecamatan|kec\.)/i', $district)) {
                        $district = 'Kecamatan ' . $district;
                    }

                    $city = $addr['city'] ?? $addr['town'] ?? $addr['regency'] ?? $addr['county'] ?? '';
                    if ($city && !preg_match('/^(kota|kabupaten|kab\.)/i', $city)) {
                        $city = 'Kota ' . $city;
                    }

                    $state = $addr['state'] ?? $addr['province'] ?? $addr['region'] ?? 'Jawa Barat';

                    // Buat baris unik tanpa duplikasi kata/kelurahan
                    $rawLines = array_filter([$roadLine, $village, $district, $city, $state]);
                    $uniqueLines = [];
                    foreach ($rawLines as $line) {
                        $trimmed = trim($line);
                        if ($trimmed !== '' && !in_array($trimmed, $uniqueLines, true)) {
                            $uniqueLines[] = $trimmed;
                        }
                    }

                    if (empty($uniqueLines)) {
                        $uniqueLines = ['Titik Lokasi Lapangan', 'Indonesia'];
                    }

                    return [
                        'success'           => true,
                        'display_name'      => $data['display_name'] ?? '',
                        'road'              => $roadLine,
                        'village'           => $village,
                        'district'          => $district,
                        'city'              => $city,
                        'state'             => $state,
                        'latitude'          => $lat,
                        'longitude'         => $lng,
                        'formatted_lines'   => $uniqueLines,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("Reverse geocode failed: " . $e->getMessage());
            }

            // Fallback jika API external tidak dapat dijangkau
            return [
                'success'         => true,
                'display_name'    => "Koordinat: {$lat}, {$lng}",
                'road'            => "Titik Lapangan",
                'village'         => "",
                'district'        => "",
                'city'            => "",
                'state'           => "Indonesia",
                'latitude'        => $lat,
                'longitude'       => $lng,
                'formatted_lines' => [
                    "Titik Lapangan",
                    "Indonesia",
                ],
            ];
        });

        return response()->json($result);
    }
}
