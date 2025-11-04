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
        
    Schema::create('service_availabilities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('service_id')->constrained()->onDelete('cascade');
        $table->date('available_date');
        $table->string('time_slot'); // e.g., "8:00 AM - 10:00 AM"
        $table->boolean('is_booked')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_availabilities');
    }
};
