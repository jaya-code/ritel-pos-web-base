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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('product_name');
            $table->string('kode_rak');
            $table->integer('periode_return');
            $table->enum('satuan', ['pcs', 'liter', 'kg', 'box', 'bks', 'rtg', 'btl', 'pck', 'tpk', 'ktk']);
            $table->decimal('cost_price', 15, 0);
            $table->decimal('selling_price', 15, 0);
            $table->decimal('stock', 15, 0);
            $table->decimal('stock_min', 15, 0)->default(0);
            $table->decimal('isi', 15, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
