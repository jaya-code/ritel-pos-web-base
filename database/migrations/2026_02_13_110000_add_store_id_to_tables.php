<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add store_id to users table - nullable because super admin doesn't need a store
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('email')->constrained('stores')->cascadeOnDelete();
        });
        
        // Update role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'owner') NOT NULL DEFAULT 'kasir'");

        // Add store_id to other tables
        $tables = ['products', 'categories', 'suppliers', 'pembelian', 'penjualan']; 
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                // Determine after column for neatness, optional but good
                $table->foreignId('store_id')->nullable()->constrained('stores')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['penjualan', 'pembelian', 'suppliers', 'categories', 'products'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        // Revert enum - potentially risky if 'owner' data exists, but standard down method
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir'");
    }
};
