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
        Schema::table('stores', function (Blueprint $table) {
            $table->json('payment_config')->nullable()->after('owner_id'); // Stores enabled methods, static image path
            $table->decimal('qris_fee', 10, 2)->default(0)->after('payment_config'); // Admin set fee
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['payment_config', 'qris_fee']);
        });
    }
};
