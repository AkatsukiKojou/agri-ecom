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
    {Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('admin_id');

        $table->string('name');
        $table->string('unit');
        $table->string('type');
        $table->decimal('price', 8, 2);
        $table->integer('stock_quantity');
        $table->text('description')->nullable();
        $table->boolean('availability')->default(true);
        $table->string('image')->nullable();
          $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        $table->softDeletes(); 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
