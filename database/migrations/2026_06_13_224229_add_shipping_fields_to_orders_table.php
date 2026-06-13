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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_code')->nullable()->after('payment_url');
            $table->string('courier_name')->nullable()->after('courier_code');
            $table->string('service_name')->nullable()->after('courier_name');
            $table->integer('shipping_cost')->default(0)->after('service_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_code', 'courier_name', 'service_name', 'shipping_cost']);
        });
    }
};
