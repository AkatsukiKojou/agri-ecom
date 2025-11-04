<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Standalone Haversine formula
function computeDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2 +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLng / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

// Standalone OpenRouteService geocoding
function getCoordinates($address) {
    $apiKey = getenv('OPENROUTESERVICE_API_KEY');
    $url = 'https://api.openrouteservice.org/geocode/search';
    $response = Http::withHeaders([
        'Authorization' => $apiKey,
    ])->get($url, [
        'text' => $address,
        'size' => 1,
    ]);
    if ($response->successful() && isset($response['features'][0]['geometry']['coordinates'])) {
        $coords = $response['features'][0]['geometry']['coordinates'];
        return [
            'lat' => $coords[1],
            'lng' => $coords[0],
        ];
    }
    return null;
}


use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;

// Bootstrap Eloquent ORM (if not already bootstrapped)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => __DIR__.'/database/database.sqlite',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Get user (first user for demo)
$user = User::first();
$defaultAddress = $user ? $user->shippingAddresses()->where('is_default', true)->first() : null;

// Get admin (first admin user)
$admin = User::where('role', 'admin')->first();
$adminProfile = $admin ? $admin->profile : null;

if ($defaultAddress && $adminProfile) {
    $userFullAddress = implode(', ', array_filter([
        $defaultAddress->barangay,
        $defaultAddress->city,
        $defaultAddress->province,
        $defaultAddress->region
    ]));
    $adminFullAddress = implode(', ', array_filter([
        $adminProfile->barangay,
        $adminProfile->city,
        $adminProfile->province,
        $adminProfile->region
    ]));

    $coords1 = getCoordinates($userFullAddress);
    $coords2 = getCoordinates($adminFullAddress);

    if ($coords1 && $coords2) {
        $distance = computeDistance($coords1['lat'], $coords1['lng'], $coords2['lat'], $coords2['lng']);
        echo "Distance between user and admin: $distance km\n";
        echo "User address: $userFullAddress\n";
        echo "Admin address: $adminFullAddress\n";
    } else {
        echo "Could not get coordinates for user or admin address.\n";
    }
} else {
    echo "Could not find user default address or admin profile.\n";
}
