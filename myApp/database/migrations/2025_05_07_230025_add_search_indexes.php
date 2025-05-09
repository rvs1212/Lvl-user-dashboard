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

        Schema::table('users', function (Blueprint $table) {
            $table->index('first_name');
            $table->index('last_name');
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->index('city');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['first_name']);
            $table->dropIndex(['last_name']);
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['country']);
        });
    }
};
