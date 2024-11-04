<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Distance extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_address',
        'destination_address',
        'mode_of_travel',
        'distance',
    ];

    public static function getDistance($origin, $destination, $mode = 'walking')
    {
        $distance = self::where('origin_address', $origin)
                        ->where('destination_address', $destination)
                        ->where('mode_of_travel', $mode)
                        ->first();

        if ($distance) {
            return $distance->distance;
        }

        // If distance doesn't exist in the database, fetch from Google Maps API
        $newDistance = self::fetchAndStoreDistance($origin, $destination, $mode);

        return $newDistance ? $newDistance->distance : null;
    }

    private static function fetchAndStoreDistance($origin, $destination, $mode)
    {
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';

        $response = Http::get($url, [
            'origins' => $origin,
            'destinations' => $destination,
            'mode' => $mode,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        $data = $response->json();
        $distance = $data['rows'][0]['elements'][0]['distance']['value'] ?? 0;

        $newDistance = self::create([
            'origin_address' => $origin,
            'destination_address' => $destination,
            'mode_of_travel' => $mode,
            'distance' => $distance,
        ]);

        return $newDistance;
    }
}
