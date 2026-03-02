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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id('pembelian_id'); // User requested purchasing_id alias if possible, or just explicit id name
            $table->string('nofak_beli');
            $table->date('tgl_beli');
            $table->unsignedBigInteger('supplier_id');
            $table->enum('jenis_pembelian', ['Kredit', 'Non Kredit', 'Titipan'])->default('Non Kredit');
            $table->enum('status_pembelian', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
            $table->enum('jenis_pembayaran', ['Tunai', 'Transfer', 'Pending'])->default('Tunai');
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->decimal('total', 15, 0);
            $table->decimal('diskon', 15, 0)->nullable();
            $table->decimal('diskon_product', 15, 0)->nullable();
            $table->decimal('ppn', 15, 0)->nullable();
            $table->decimal('grand_total', 15, 0);
            $table->date('tgl_bayar')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id(); // Standard ID for detail row, optional but good practice
            $table->unsignedBigInteger('pembelian_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('qty_beli', 15, 0);
            $table->decimal('harga_beli', 15, 0);
            $table->decimal('disc', 15, 0)->default(0);
            $table->decimal('sub_total', 15, 0);
            $table->timestamps();

            $table->foreign('pembelian_id')->references('pembelian_id')->on('pembelian')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_details');
        Schema::dropIfExists('pembelian');
    }
};
