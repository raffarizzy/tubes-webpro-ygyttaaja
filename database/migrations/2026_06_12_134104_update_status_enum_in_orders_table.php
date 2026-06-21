<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan raw SQL karena mengubah ENUM terkadang bermasalah dengan schema builder standar
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'shipped', 'cancelled') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
        }
    }
};
