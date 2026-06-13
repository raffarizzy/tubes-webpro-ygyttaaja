<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alamats', function (Blueprint $table) {
            $table->string('provinsi')->nullable()->after('alamat');
            $table->string('kota')->nullable()->after('provinsi');
            $table->string('kecamatan')->nullable()->after('kota');
            $table->string('kode_wilayah')->nullable()->after('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('alamats', function (Blueprint $table) {
            $table->dropColumn(['provinsi', 'kota', 'kecamatan', 'kode_wilayah']);
        });
    }
};
