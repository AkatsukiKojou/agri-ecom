<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_note')->nullable();
            $table->string('phone')->nullable();
            $table->date('downpayment_visit_date')->nullable();

            // New address fields for easier filtering
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['customer_note','phone', 'downpayment_visit_date', 'region', 'province', 'city', 'barangay']);
        });
    }
};
