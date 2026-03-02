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
        Schema::table('products', function (Blueprint $table) {
            $table->string('kode_rak')->nullable()->change();
            $table->integer('periode_return')->nullable()->change();
            $table->decimal('isi', 15, 0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('kode_rak')->nullable(false)->change();
            $table->integer('periode_return')->nullable(false)->change();
            $table->decimal('isi', 15, 0)->nullable(false)->change();
        });
    }
};
