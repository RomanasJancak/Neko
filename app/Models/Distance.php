<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

use Carbon\Carbon;

class Distance extends Model
{
    use HasFactory;
    // TODO: This TTL is temporary and hard-coded. 
    // Consider moving to a config file or .env
    private static $maxDaysToKeepDistanceStored = 10;
    
    /**
     * Request-level cache to prevent N+1 queries during route calculations.
     * Cleared after each request automatically.
     */
    private static $requestCache = [];

    protected $fillable = [
        'origin_address',
        'destination_address',
        'origin_address_id',
        'destination_address_id',
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'mode_of_travel',
        'distance',
    ];

    public static function getDistance($origin, $destination, $mode = 'walking')
    {
        // Check request-level cache first to prevent N+1 queries
        $cacheKey = md5($origin . '|' . $destination . '|' . $mode);
        if (isset(self::$requestCache[$cacheKey])) {
            return self::$requestCache[$cacheKey];
        }
        
        $distance = self::where('origin_address', $origin)
                        ->where('destination_address', $destination)
                        ->where('mode_of_travel', $mode)
                        ->first();

      if ($distance) {
            $isStale = $distance->updated_at->diffInDays(Carbon::now()) >= self::$maxDaysToKeepDistanceStored;

            if (!$isStale) {
                // Cache the result for this request
                self::$requestCache[$cacheKey] = $distance->distance;
                return $distance->distance;
            }

            $distance->delete();
      }

        $newDistance = self::fetchAndStoreDistance($origin, $destination, $mode);
        $result = $newDistance ? $newDistance->distance : null;
        
        // Cache the result for this request
        self::$requestCache[$cacheKey] = $result;
        
        return $result;
    }
    
    /**
     * Clear the request-level cache. Useful for testing.
     */
    public static function clearRequestCache()
    {
        self::$requestCache = [];
    }
    
    private static function geocodeAddress($address)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        $data = $response->json();

        if (($data['status'] ?? '') === 'OK' && !empty($data['results'])) {
            $result = $data['results'][0];
            return [
                'lat'      => $result['geometry']['location']['lat'],
                'lng'      => $result['geometry']['location']['lng'],
                'place_id' => $result['place_id'], // Captured for origin_address_id
            ];
        }

        return null;
    }
    private static function fetchAndStoreDistance($origin, $destination, $mode)
    {
      $originData = self::geocodeAddress($origin);
      $destData = self::geocodeAddress($destination);

        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';

        $response = Http::get($url, [
            'origins' => "place_id:{$originData['place_id']}",
            'destinations' => "place_id:{$destData['place_id']}",
            'mode' => $mode,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        $data = $response->json();
        //$distance = $data['rows'][0]['elements'][0]['distance']['value'] ?? 0;
        if (($data['status'] ?? '') !== 'OK') return null;
        
        $element = $data['rows'][0]['elements'][0] ?? null;
        if (!$element || $element['status'] !== 'OK') return null;

        // $newDistance = self::create([
        //     'origin_address' => $origin,
        //     'destination_address' => $destination,
        //     'mode_of_travel' => $mode,
        //     'distance' => $distance,
        // ]);

      return self::create([
            'origin_address'         => $origin,
            'destination_address'    => $destination,
            'origin_address_id'      => $originData['place_id'],
            'destination_address_id' => $destData['place_id'],
            'origin_lat'             => $originData['lat'],
            'origin_lng'             => $originData['lng'],
            'destination_lat'        => $destData['lat'],
            'destination_lng'        => $destData['lng'],
            'mode_of_travel'         => $mode,
            'distance'               => $element['distance']['value'],
      ]);
        return $newDistance;
    }
}
