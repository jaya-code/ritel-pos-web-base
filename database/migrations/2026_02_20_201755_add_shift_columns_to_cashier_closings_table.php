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
        Schema::table('cashier_closings', function (Blueprint $table) {
            $table->decimal('opening_cash', 15, 2)->default(0)->after('user_id');
            $table->enum('status', ['open', 'closed'])->default('closed')->after('opening_cash');
            
            // Make closing amounts nullable because they are not known at opening time
            $table->decimal('system_cash', 15, 2)->nullable()->change();
            $table->decimal('actual_cash', 15, 2)->nullable()->change();
            $table->decimal('difference', 15, 2)->nullable()->change();
            $table->decimal('total_tunai', 15, 2)->nullable()->change();
            $table->decimal('total_qris_dinamis', 15, 2)->nullable()->change();
            $table->decimal('total_qris_statis', 15, 2)->nullable()->change();
            $table->decimal('total_transfer', 15, 2)->nullable()->change();
            $table->decimal('total_debit', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_closings', function (Blueprint $table) {
            $table->dropColumn(['opening_cash', 'status']);
            
            $table->decimal('system_cash', 15, 2)->nullable(false)->change();
            $table->decimal('actual_cash', 15, 2)->nullable(false)->change();
            $table->decimal('difference', 15, 2)->nullable(false)->change();
            $table->decimal('total_tunai', 15, 2)->nullable(false)->change();
            $table->decimal('total_qris_dinamis', 15, 2)->nullable(false)->change();
            $table->decimal('total_qris_statis', 15, 2)->nullable(false)->change();
            $table->decimal('total_transfer', 15, 2)->nullable(false)->change();
            $table->decimal('total_debit', 15, 2)->nullable(false)->change();
        });
    }
};
