<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToUsersEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // If the index already exists this will fail, so guard with DB check in most environments.
            // For a safer migration in production you might want to wrap in try/catch.
            $table->unique('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
}
