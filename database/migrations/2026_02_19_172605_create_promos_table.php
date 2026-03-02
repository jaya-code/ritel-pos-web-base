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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['simple_discount', 'buy_x_get_y', 'bundle']);
            
            // Target Product (The item being bought or discounted)
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            
            // Reward Product (For Buy X Get Y - if different from product_id)
            $table->foreignId('reward_product_id')->nullable()->constrained('products')->cascadeOnDelete();

            // Discount Details
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable(); // For simple_discount
            $table->decimal('discount_value', 15, 2)->nullable(); // Amount or Percentage value
            
            // Bundle Details
            $table->decimal('bundle_price', 15, 2)->nullable(); // For bundle type
            
            // Quantity Logic
            $table->integer('buy_qty')->nullable(); // Buy 3 (bundle) or Buy 1 (BOGO)
            $table->integer('get_qty')->nullable(); // Get 1 (BOGO)

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
