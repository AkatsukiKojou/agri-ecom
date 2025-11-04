<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'ongoing' to the bookings.status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled', 'no show', 'completed', 'ongoing') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'ongoing' from enum (revert to previous)
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled', 'no show', 'completed') DEFAULT 'pending'");
    }
};
