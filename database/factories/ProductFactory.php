<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $costPrice = $this->faker->numberBetween(1000, 100000);
        $sellingPrice = $costPrice + ($costPrice * 0.2); // 20% margin

        return [
            'barcode' => $this->faker->unique()->ean13,
            'sku' => $this->faker->unique()->bothify('SKU-####-????'),
            'product_name' => $this->faker->words(3, true),
            // category_id and supplier_id will be assigned by seeder or factory states
            'kode_rak' => $this->faker->bothify('Rak-##'),
            'periode_return' => 30,
            'satuan' => $this->faker->randomElement(['pcs', 'kg', 'liter', 'box']),
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock' => $this->faker->numberBetween(10, 100),
            'stock_min' => 5,
            'isi' => 1,
            // store_id will be assigned by seeder
        ];
    }
}
