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
        // Drop old tables if they exist
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');

        Schema::create('penjualan', function (Blueprint $table) {
            $table->string('penjualan_id')->primary();
            $table->string('invoice');
            $table->dateTime('tgl_penjualan');
            $table->decimal('potongan_harga',15,0)->default(0);
            $table->decimal('total_harga',15,0);
            $table->decimal('jumlah_uang',15,0);
            $table->decimal('uang_kembali',15,0);
            $table->unsignedBigInteger('user_id'); // Changed to unsigned to match users.id
            $table->decimal('total',15,0);
            $table->enum('metode_pembayaran',['Tunai','Qris Statis','Qris Dinamis','Debit']);
            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('id');
        });

        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id(); // Adding an ID for valid model operations usually, or just using composite keys? User didn't specify ID, but Laravel models usually like one. I'll stick to their schema strictly regarding columns but might need a PK. They didn't specify PK. The previous schema had one. creating a standard id is safer. Or I can just omit it if they used 'string penjualan_id' as key in parent.
            // Actually, I'll follow their schema exactly for columns, but for 'penjualan_details' usually you want a PK. 
            // The provided schema:
            // $table->string('penjualan_id');
            // ...
            // Valid. 
            $table->string('penjualan_id');
            $table->unsignedBigInteger('product_id'); // Match products.id
            $table->integer('qty_jual');
            $table->decimal('harga_beli',15,0); // Cost price
            $table->decimal('harga_jual',15,0); // Selling price
            $table->decimal('sub_total',15,0);
            $table->decimal('diskon',15,0)->default(0);
            $table->decimal('total',15,0);
            $table->timestamps();

            $table->foreign('penjualan_id')->on('penjualan')->references('penjualan_id')->onDelete('cascade');
            $table->foreign('product_id')->on('products')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
        Schema::dropIfExists('penjualan');
    }
};
