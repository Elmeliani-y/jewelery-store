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
        // Seed jewelry system data first to create branches
        $this->call([
            JewelrySystemSeeder::class,
            // FakeSalesExpensesSeeder::class,
        ]);

        // Get branches for creating branch users
        $branches = \App\Models\Branch::all();

        // Create default admin user
        User::factory()->create([
            'name' => 'مدير النظام',
            'username' => 'admin',
            'email' => 'admin@dusty.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'branch_id' => null,
            'remember_token' => Str::random(10),
        ]);

        // Create accountant user
        User::factory()->create([
            'name' => 'المحاسب',
            'username' => 'accountant',
            'email' => 'accountant@dusty.com',
            'email_verified_at' => now(),
            'password' => Hash::make('accountant123'),
            'role' => 'accountant',
            'branch_id' => null,
            'remember_token' => Str::random(10),
        ]);

        // Create branch users for each branch
        if ($branches->count() > 0) {
            foreach ($branches as $branch) {
                $branchSlug = preg_replace('/[^a-z0-9]+/i', '', $branch->name);
                $username = 'branch_'.$branch->id;

                User::factory()->create([
                    'name' => 'حساب '.$branch->name,
                    'username' => $username,
                    'email' => $username.'@dusty.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('branch123'),
                    'role' => 'branch',
                    'branch_id' => $branch->id,
                    'remember_token' => Str::random(10),
                ]);
            }
        }
    }
}
