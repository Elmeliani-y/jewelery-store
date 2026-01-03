<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only create users if they do not already exist
        if (!User::where('username', 'accountant')->exists()) {
            User::factory()->create([
                'name' => 'المحاسب',
                'username' => 'accountant',
                'password' => Hash::make('accountant123'),
                'role' => 'accountant',
                'branch_id' => null,
                'remember_token' => Str::random(10),
            ]);
        }

        if (!User::where('username', 'admin')->exists()) {
            User::factory()->create([
                'name' => 'المدير',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'branch_id' => null,
                'remember_token' => Str::random(10),
            ]);
        }

        // Seed system data (branches, categories, calibers, etc.)
        $this->call([
            JewelrySystemSeeder::class,
        ]);

        // Seed fake sales/expenses data for testing
        $this->call([
            FakeSalesExpensesSeeder::class,
        ]);
    }
}
