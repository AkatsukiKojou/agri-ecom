<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sender_id');
        $table->unsignedBigInteger('receiver_id');
        $table->text('message')->nullable();
        $table->string('image')->nullable();
        $table->unsignedBigInteger('service_id')->nullable();
        $table->string('service_name')->nullable();
        $table->string('service_image')->nullable();
        $table->string('service_type')->nullable();
        $table->string('service_price')->nullable();
        $table->boolean('is_read')->default(0);

        $table->timestamps();

        $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
