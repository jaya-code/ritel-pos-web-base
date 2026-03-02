<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SupPiler; // Typo in original file? No, it was Supplier. Checking model.
use App\Models\Supplier;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $catFood = Category::firstOrCreate(['name' => 'Makanan'], ['description' => 'Makanan Ringan & Berat']);
        $catBev = Category::firstOrCreate(['name' => 'Minuman'], ['description' => 'Minuman Dingin & Panas']);
        $catSembako = Category::firstOrCreate(['name' => 'Sembako'], ['description' => 'Kebutuhan Pokok']);

        // Create Suppliers
        $supA = Supplier::firstOrCreate(['name' => 'Agen A'], ['contact_info' => '081234567890', 'address' => 'Jl. Merdeka No. 1']);
        $supB = Supplier::firstOrCreate(['name' => 'Agen B'], ['contact_info' => '089876543210', 'address' => 'Jl. Sudirman No. 2']);

        // Create Products
        Product::firstOrCreate(['barcode' => '8991234567890'], [
            'product_name' => 'Minyak Goreng 2L',
            'category_id' => $catSembako->id,
            'supplier_id' => $supA->id,
            'kode_rak' => 'A1',
            'periode_return' => 30,
            'satuan' => 'liter',
            'cost_price' => 28000,
            'selling_price' => 32000,
            'stock' => 50,
            'stock_min' => 5,
            'isi' => 1,
        ]);

        Product::firstOrCreate(['barcode' => '8990987654321'], [
            'product_name' => 'Beras Premium 5kg',
            'category_id' => $catSembako->id,
            'supplier_id' => $supA->id,
            'kode_rak' => 'A2',
            'periode_return' => 30,
            'satuan' => 'kg',
            'cost_price' => 65000,
            'selling_price' => 72000,
            'stock' => 20,
            'stock_min' => 2,
            'isi' => 1,
        ]);

        Product::firstOrCreate(['barcode' => '8991122334455'], [
            'product_name' => 'Teh Botol 500ml',
            'category_id' => $catBev->id,
            'supplier_id' => $supB->id,
            'kode_rak' => 'B1',
            'periode_return' => 30,
            'satuan' => 'btl',
            'cost_price' => 4500,
            'selling_price' => 6000,
            'stock' => 100,
            'stock_min' => 10,
            'isi' => 1,
        ]);

        Product::firstOrCreate(['barcode' => '8995544332211'], [
            'product_name' => 'Kopi Susu Kaleng',
            'category_id' => $catBev->id,
            'supplier_id' => $supB->id,
            'kode_rak' => 'B2',
            'periode_return' => 30,
            'satuan' => 'pcs',
            'cost_price' => 7000,
            'selling_price' => 9500,
            'stock' => 40,
            'stock_min' => 5,
            'isi' => 1,
        ]);

        Product::firstOrCreate(['barcode' => '8996677889900'], [
            'product_name' => 'Keripik Singkong',
            'category_id' => $catFood->id,
            'supplier_id' => $supA->id,
            'kode_rak' => 'C1',
            'periode_return' => 30,
            'satuan' => 'bks',
            'cost_price' => 8000,
            'selling_price' => 12000,
            'stock' => 30,
            'stock_min' => 5,
            'isi' => 1,
        ]);
        
        Product::firstOrCreate(['barcode' => '12345'], [
            'product_name' => 'Test Item Short Barcode',
            'category_id' => $catFood->id,
            'supplier_id' => $supA->id,
            'kode_rak' => 'Z1',
            'periode_return' => 30,
            'satuan' => 'pcs',
            'cost_price' => 1000,
            'selling_price' => 2000,
            'stock' => 1000,
            'stock_min' => 10,
            'isi' => 1,
        ]);
    }
}
