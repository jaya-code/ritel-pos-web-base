<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product; // Fixed typo SupPiler -> Supplier
use Illuminate\Support\Facades\Hash; // Import Hash

class TestOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create User "Test Owner 2"
        $user = User::firstOrCreate(
            ['email' => 'owner2@rpos.com'],
            [
                'name' => 'Test Owner 2',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        // 2. Create Store for this user (if not exists)
        // Check if user already has a store_id
        if ($user->store_id) {
            $store = Store::find($user->store_id);
        } else {
             // Create a new store
            $store = Store::create([
                'name' => 'Toko Test Owner 2',
                'address' => 'Jl. Test Owner 2 No. 123',
                'phone' => '08123456789',
                'owner_id' => $user->id,
            ]);

            // Update user with store_id
            $user->store_id = $store->id;
            $user->save();
        }

        // 3. Create Categories for this store (if fewer than 5)
        if (Category::where('store_id', $store->id)->count() < 5) {
            Category::factory(5)->create(['store_id' => $store->id]);
        }
        $categories = Category::where('store_id', $store->id)->get();

        // 4. Create Suppliers for this store (if fewer than 5)
        if (Supplier::where('store_id', $store->id)->count() < 5) {
            Supplier::factory(5)->create(['store_id' => $store->id]);
        }
        $suppliers = Supplier::where('store_id', $store->id)->get();

        // 5. Create 500 Products
        $this->command->info('Seeding 500 products for Test Owner 2...');
        
        // Use a loop to assign random category and supplier
        // We can batch this slightly for performance if needed, but 500 is small.
        $batchSize = 50;
        $total = 500;
        
        for ($i = 0; $i < $total; $i++) {
            Product::factory()->create([
                'store_id' => $store->id,
                'category_id' => $categories->random()->id,
                'supplier_id' => $suppliers->random()->id,
            ]);
            
            if (($i + 1) % $batchSize == 0) {
                 $this->command->info("Created " . ($i + 1) . " products...");
            }
        }

        $this->command->info('Done! User: owner2@rpos.com / password');
    }
}
