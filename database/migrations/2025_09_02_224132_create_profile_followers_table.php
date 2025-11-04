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
    Schema::create('profile_followers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();
        $table->unique(['profile_id', 'user_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_followers');
    }
};
