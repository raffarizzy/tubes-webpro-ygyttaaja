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
            $table->string('phone')->nullable()->change();
            $table->date('birthDate')->nullable()->change();
            $table->enum('gender', ['male', 'female'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->date('birthDate')->nullable(false)->change();
            $table->enum('gender', ['male', 'female'])->nullable(false)->change();
        });
    }
};
