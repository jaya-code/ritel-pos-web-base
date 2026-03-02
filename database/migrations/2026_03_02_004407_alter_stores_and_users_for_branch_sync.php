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
            if (Schema::hasColumn('users', 'subscription_until')) {
                $table->dropColumn('subscription_until');
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'subscription_until')) {
                $table->dropColumn('subscription_until');
            }

            $table->string('api_url')->nullable()->after('qris_fee');
            $table->string('api_token')->nullable()->after('api_url');
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending')->after('api_token');
            $table->timestamp('last_sync_at')->nullable()->after('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('subscription_until')->nullable()->after('role');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->timestamp('subscription_until')->nullable()->after('qris_fee');
            $table->dropColumn(['api_url', 'api_token', 'sync_status', 'last_sync_at']);
        });
    }
};
