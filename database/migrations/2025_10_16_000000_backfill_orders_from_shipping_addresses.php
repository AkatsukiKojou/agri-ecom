<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For each order missing address or email, try to populate it from the user's default shipping address
        $orders = DB::table('orders')->whereNull('address')->orWhere('address', '')->get();
        foreach ($orders as $order) {
            $sa = DB::table('shipping_addresses')
                ->where('user_id', $order->user_id)
                ->where('is_default', true)
                ->first();

            if ($sa) {
                $formatted = trim(implode(', ', array_filter([
                    $sa->address ?? null,
                    $sa->barangay ?? null,
                    $sa->city ?? null,
                    $sa->province ?? null,
                    $sa->region ?? null,
                ])));

                DB::table('orders')->where('id', $order->id)->update([
                    'address' => $formatted ?: ($sa->address ?? null),
                    'email' => $order->email ?? $sa->email ?? null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse safely; do not revert data changes automatically.
    }
};
