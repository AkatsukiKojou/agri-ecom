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
          Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('admin_id')->nullable();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('service_id')->constrained()->onDelete('cascade');
$table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('cancel_reason')->nullable();

        $table->date('booking_start')->nullable();
        $table->string('full_name')->nullable();
         $table->integer('attendees')->nullable();
        $table->decimal('total_price', 8, 2)->nullable();
        $table->string('payment_method')->nullable();
        $table->decimal('downpayment', 10, 2)->nullable();


        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
