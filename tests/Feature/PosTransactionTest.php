<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_search_product()
    {
        // Setup
        $category = Category::create(['name' => 'Demo Cat']);
        $supplier = Supplier::create(['name' => 'Demo Sup']);
        $product = Product::create([
            'barcode' => 'TEST12345',
            'product_name' => 'Test Product',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'kode_rak' => 'A1',
            'periode_return' => 30,
            'satuan' => 'pcs',
            'cost_price' => 5000,
            'selling_price' => 10000,
            'stock' => 100,
            'stock_min' => 10,
            'isi' => 1,
        ]);

        // Act
        $response = $this->postJson(route('pos.search'), ['barcode' => 'TEST12345']);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonPath('products.0.product_name', 'Test Product');
    }

    public function test_pos_store_transaction()
    {
        // Setup
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Demo Cat']);
        $supplier = Supplier::create(['name' => 'Demo Sup']);
        $product = Product::create([
            'barcode' => 'TEST12345',
            'product_name' => 'Test Product',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'kode_rak' => 'A1',
            'periode_return' => 30,
            'satuan' => 'pcs',
            'cost_price' => 5000,
            'selling_price' => 10000,
            'stock' => 100,
            'stock_min' => 10,
            'isi' => 1,
        ]);

        $cartData = [
            [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => $product->selling_price,
                'quantity' => 2
            ]
        ];

        // Act
        $response = $this->actingAs($user)->post(route('pos.store'), [
            'cart_data' => json_encode($cartData),
            'amount_paid' => 50000, 
            'payment_method' => 'Tunai'
        ]);

        // Assert
        $response->assertRedirect(route('pos.index'));
        
        // Assert Penjualan Table
        $this->assertDatabaseHas('penjualan', [
            'total_harga' => 20000,
            'jumlah_uang' => 50000,
            'uang_kembali' => 30000,
            'metode_pembayaran' => 'Tunai'
        ]);

        // Assert PenjualanDetails Table
        $this->assertDatabaseHas('penjualan_details', [
            'product_id' => $product->id,
            'qty_jual' => 2,
            'harga_jual' => 10000,
            'sub_total' => 20000
        ]);
        
        // Assert Stock Decrement
        $this->assertEquals(98, $product->fresh()->stock);
    }
}
