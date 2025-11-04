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
         Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
$table->foreignId('admin_id')->nullable()->constrained('users');

        $table->string('name');
        $table->string('phone');
        $table->string('address');
        $table->decimal('total_price', 10, 2);
              $table->decimal('shipping_fee', 8, 2)->default(0);

        $table->string('status')->default('pending'); // e.g. pending, confirmed, shipped, delivered
        $table->string('payment_method')->default('manual'); // e.g. manual, gcash, cod, etc.
         $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        $table->timestamps();
           });
           
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
