<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('role', 'owner')->first();
        if ($user && $user->store) {
            Member::create([
                'store_id' => $user->store->id,
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 1',
                'point' => 100
            ]);

            Member::create([
                'store_id' => $user->store->id,
                'name' => 'Siti Aminah',
                'phone' => '089876543210',
                'address' => 'Jl. Sudirman No. 2',
                'point' => 50
            ]);
        }
    }
}
