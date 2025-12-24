<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('alamat');
            $table->boolean('is_default')->default(false); // UBAH DARI isDefault
            $table->string('nama_penerima');
            $table->string('nomor_penerima');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alamats');
    }
};