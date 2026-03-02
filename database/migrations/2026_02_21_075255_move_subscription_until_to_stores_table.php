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
            $table->timestamp('subscription_until')->nullable();
        });

        // Move existing subscription data from users to their stores
        \Illuminate\Support\Facades\DB::update('
            UPDATE stores s
            JOIN users u ON u.id = s.owner_id
            SET s.subscription_until = u.subscription_until
            WHERE u.subscription_until IS NOT NULL
        ');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subscription_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('subscription_until')->nullable();
        });

        // Restore subscription data (assuming one store per owner)
        \Illuminate\Support\Facades\DB::update('
            UPDATE users u
            JOIN stores s ON u.id = s.owner_id
            SET u.subscription_until = s.subscription_until
            WHERE s.subscription_until IS NOT NULL
        ');

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('subscription_until');
        });
    }
};
