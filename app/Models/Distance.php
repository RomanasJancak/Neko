<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Http;

class Distance extends Model
{
    use HasFactory;

    protected $apiKey;
    protected $fillable = [
        'origin_address',
        'destination_address',
        'origin_coordinates',
        'destination_coordinates',
        'mode_of_travel',
        'distance',
    ];
    public static function getDistance($origin, $destination, $mode = 'walking')
    {
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
        $distance = self::where('origin_address', $origin)
        ->where('destination_address', $destination)
        ->where('mode_of_travel', $mode)
        ->first();
        if (!$distance) {
                // If distance doesn't exist in the database, fetch from Google Maps API
                //$distance = self::fetchAndStoreDistance($origin, $destination, $mode);
            
            $response = Http::get($url, [
                'origins' => $origin,
                'destinations' => $destination,
                'mode'  =>  $mode,
                'key' => env('GOOGLE_MAPS_API_KEY'),
            ]);
            $data = $response->json();
            if (isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                $distance = $data['rows'][0]['elements'][0]['distance']['value'];
                Distance::create([
                    'origin_address' => $origin,
                    'destination_address' => $destination,
                    'mode_of_travel' => $mode,
                    'distance' => $distance,
                ]);
            }else{
                $distance = 0;
            }
        }
        return $distance->distance;
    }
    public static function getDistance2($origin, $destination, $mode = 'walking')
    {
        $distance = self::where('origin_address', $origin)
            ->where('destination_address', $destination)
            ->where('mode_of_travel', $mode)
            ->first();
        if (!$distance) {
            // If distance doesn't exist in the database, fetch from Google Maps API
            $distance = self::fetchAndStoreDistance($origin, $destination, $mode);
        }

        return $distance ? $distance->distance : null;
    }

    private static function fetchAndStoreDistance($origin, $destination, $mode)
    {
        // Call Google Maps API to get distance
        // Use your preferred method or the previously described GuzzleHTTP method to make the API request
        // Once you have the distance data from the API, store it in the database
        // Don't forget to handle storing coordinates and other necessary data in the table

        // Example of storing data:
        $newDistance = Distance::create([
            'origin_address' => $origin,
            'destination_address' => $destination,
            'mode_of_travel' => $mode,
            'distance' => Distance::calculateDistance($origin,$destination,$mode), // Replace with the actual distance obtained from the API
            // Store other necessary data
        ]);

        return $newDistance;
    }
    private static function calculateDistance($origin, $destination, $mode = 'walking') {
        $distance = \GoogleMaps::load('distancematrix')                    
        ->setParamByKey('origins', $origin)
        ->setParamByKey('destinations', $destination)   
        ->setParamByKey('mode', $mode)
        ->setParamByKey('language', 'LT')                   
        ->getResponseByKey('rows.elements')['rows'][0]['elements'][0]['distance']['value'];
    
        // Calculate distance between addresses for specified travel mode
        return $distance;
        //$drivingDistance = calculateDistance($originAddress, $destinationAddress, 'driving');
        //$walkingDistance = calculateDistance($originAddress, $destinationAddress, 'walking');
        //$cyclingDistance = calculateDistance($originAddress, $destinationAddress, 'bicycling');
    }
}
