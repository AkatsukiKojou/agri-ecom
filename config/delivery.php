    <?php

return [
    'base_fee' => env('DELIVERY_BASE_FEE', 30),         // Base delivery fee (₱)
    'base_distance' => env('DELIVERY_BASE_DISTANCE', 3), // Base distance (km)
    'per_km_rate' => env('DELIVERY_PER_KM_RATE', 10),    // Rate per km beyond base (₱)
    'extra_item_fee' => env('DELIVERY_EXTRA_ITEM_FEE', 5), // Extra item fee (₱)
];