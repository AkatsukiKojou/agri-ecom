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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->unsignedBigInteger('admin_id');
             $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('farm_name');
            $table->string('farm_owner');
            $table->string('region');
            $table->string('province');
            $table->string('city');
            $table->string('barangay');   
                    $table->string('address')->nullable();
                 $table->string('gcash_qr')->nullable();

            $table->text('description');
            $table->string('phone_number')->nullable();
            $table->string('email');
            $table->string('profile_photo')->nullable();
            $table->string('certificate');
            $table->string('farm_photo');
            $table->string('documentary')->nullable();
            $table->json('farm_gallery')->nullable();
             $table->unsignedBigInteger('followers')->default(0);
        $table->unsignedBigInteger('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
