<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Caliber;
use App\Models\Category;
use App\Models\Employee;
use App\Models\ExpenseType;
use App\Models\Setting;
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
        // Create branches
        $branch1 = Branch::create([
            'name' => 'الفرع الرئيسي',
            'address' => 'شارع الملك فهد، الرياض',
            'phone' => '0112345678',
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'name' => 'فرع جدة',
            'address' => 'شارع التحلية، جدة',
            'phone' => '0122345678',
            'is_active' => true,
        ]);

        // Create calibers
        $caliber18 = Caliber::create([
            'name' => 'عيار 18',
            'tax_rate' => 15.00,
            'is_active' => true,
        ]);

        $caliber21 = Caliber::create([
            'name' => 'عيار 21',
            'tax_rate' => 15.00,
            'is_active' => true,
        ]);

        $caliber24 = Caliber::create([
            'name' => 'عيار 24',
            'tax_rate' => 15.00,
            'is_active' => true,
        ]);

        // Create categories
        Category::create([
            'name' => 'خواتم',
            'is_active' => true,
            'default_caliber_id' => $caliber18->id,
        ]);

        Category::create([
            'name' => 'أساور',
            'is_active' => true,
            'default_caliber_id' => $caliber21->id,
        ]);

        Category::create([
            'name' => 'قلائد',
            'is_active' => true,
            'default_caliber_id' => $caliber18->id,
        ]);

        Category::create([
            'name' => 'أقراط',
            'is_active' => true,
            'default_caliber_id' => $caliber18->id,
        ]);

        // Create employees
        Employee::create([
            'name' => 'أحمد محمد',
            'phone' => '0501234567',
            'email' => 'ahmed@example.com',
            'salary' => 5000.00,
            'branch_id' => $branch1->id,
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'فاطمة علي',
            'phone' => '0509876543',
            'email' => 'fatima@example.com',
            'salary' => 4500.00,
            'branch_id' => $branch1->id,
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'خالد عبدالله',
            'phone' => '0503456789',
            'email' => 'khaled@example.com',
            'salary' => 4800.00,
            'branch_id' => $branch2->id,
            'is_active' => true,
        ]);

        // Create expense types
        ExpenseType::create([
            'name' => 'رواتب',
            'is_active' => true,
        ]);

        ExpenseType::create([
            'name' => 'إيجار',
            'is_active' => true,
        ]);

        ExpenseType::create([
            'name' => 'كهرباء',
            'is_active' => true,
        ]);

        ExpenseType::create([
            'name' => 'صيانة',
            'is_active' => true,
        ]);

        ExpenseType::create([
            'name' => 'تسويق',
            'is_active' => true,
        ]);

        // Create settings
        Setting::create([
            'key' => 'shop_name',
            'value' => 'مجوهرات داستي',
        ]);

        Setting::create([
            'key' => 'shop_phone',
            'value' => '0112345678',
        ]);

        Setting::create([
            'key' => 'shop_email',
            'value' => 'info@dusty-jewelry.com',
        ]);

        Setting::create([
            'key' => 'default_tax_rate',
            'value' => '15',
        ]);

        Setting::create([
            'key' => 'currency',
            'value' => 'SAR',
        ]);

        // Create accountant user
        User::factory()->create([
            'name' => 'المحاسب',
            'username' => 'accountant',
            'password' => Hash::make('accountant123'),
            'role' => 'accountant',
            'branch_id' => null,
            'remember_token' => Str::random(10),
        ]);
    }
}
