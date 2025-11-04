<?php

namespace App\Services;

use App\Models\User;
use App\Models\Products;

class ShippingFeeService
{
    /**
     * Calculate shipping fee and breakdown for given cart and selected products.
     * Returns [shipping_fee, shipping_breakdown, total]
     */
    public function calculate(
        array $cart,
        array $selected,
        ?User $user,
        callable $getCoordinates
    ) {
        // Group items by seller (admin_id)
        $itemsBySeller = [];
        foreach ($selected as $productId) {
            if (isset($cart[$productId])) {
                $product = Products::find($productId);
                if ($product) {
                    $sellerId = $product->admin_id ?? $product->user_id ?? null;
                    if ($sellerId) {
                        $itemsBySeller[$sellerId][] = [
                            'product' => $product,
                            'cart_item' => $cart[$productId],
                        ];
                    }
                }
            }
        }

        // Get user shipping address
        $userFullAddress = null;
        $userCoords = null;
        if ($user && method_exists($user, 'shippingAddresses')) {
            $defaultAddress = $user->shippingAddresses()->where('is_default', true)->first();
            if ($defaultAddress) {
                $userFullAddress = implode(', ', array_filter([
                    $defaultAddress->barangay,
                    $defaultAddress->city,
                    $defaultAddress->province,
                    $defaultAddress->region
                ]));
                $userCoords = $getCoordinates($userFullAddress);
            }
        }

        $shipping_fee = 0;
        $shipping_breakdown = [];
        $total = 0;

        foreach ($itemsBySeller as $sellerId => $items) {
            $seller = User::find($sellerId);
            $profile = $seller ? $seller->profile : null;
            if (!$profile) continue;
            $sellerFullAddress = implode(', ', array_filter([
                $profile->barangay,
                $profile->city,
                $profile->province,
                $profile->region
            ]));
            $sellerCoords = $getCoordinates($sellerFullAddress);

            $distance = 0;
            $base_fee = 20;
            $base_distance = 3;
            $rate_per_km = 5;
            $extra_item_fee = 40;

            if ($userCoords && $sellerCoords) {
                $distance = $this->computeDistance(
                    $userCoords['lat'], $userCoords['lng'],
                    $sellerCoords['lat'], $sellerCoords['lng']
                );
                if ($distance > 100) {
                    $distance = 100;
                }
            }

            $seller_total_items = 0;
            $seller_total = 0;
            foreach ($items as $entry) {
                $seller_total_items += $entry['cart_item']['quantity'];
                $seller_total += $entry['cart_item']['price'] * $entry['cart_item']['quantity'];
            }
            $extra_distance = max(0, $distance - $base_distance);
            $extra_distance_fee = $extra_distance * $rate_per_km;
            $extra_items = max(0, $seller_total_items - 1);
            $extra_item_fee_total = $extra_items * $extra_item_fee;
            $seller_shipping_fee = $base_fee + $extra_distance_fee + $extra_item_fee_total;

            if (!$userCoords || !$sellerCoords) {
                $distance = 0;
                $extra_distance_fee = 0;
                if (!$userCoords) {
                    // No default user address -> do not charge shipping until address is set
                    $seller_shipping_fee = 0;
                } else {
                    // User coords exist but seller coords missing -> apply base fallback
                    $seller_shipping_fee = $base_fee + $extra_item_fee_total;
                }
            }

            $shipping_fee += $seller_shipping_fee;
            $total += $seller_total;
            $shipping_breakdown[$sellerId] = [
                'seller' => $seller,
                'profile' => $profile,
                'items' => $items,
                'distance' => $distance,
                'shipping_fee' => $seller_shipping_fee,
                'total' => $seller_total,
            ];
        }

        return [$shipping_fee, $shipping_breakdown, $total];
    }

    protected function computeDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
