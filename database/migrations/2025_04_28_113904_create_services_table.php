<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('images')->nullable();
            $table->string('service_name')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('unit')->nullable(); // unit of measurement for the service
            $table->time('start_time');
            $table->string('duration')->nullable(); // Duration field
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};