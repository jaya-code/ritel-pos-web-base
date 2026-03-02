<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@rpos.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Create Kasir
        User::updateOrCreate(
            ['email' => 'kasir@rpos.com'],
            [
                'name' => 'Kasir 1',
                'password' => bcrypt('password'),
                'role' => 'kasir',
            ]
        );
    }
}
