<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Caliber;
use App\Models\ExpenseType;
use Illuminate\Database\Seeder;

class JewelrySystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default calibers
        $calibers = [
            ['name' => '24', 'tax_rate' => 10.00],
            ['name' => '22', 'tax_rate' => 8.50],
            ['name' => '21', 'tax_rate' => 7.75],
            ['name' => '18', 'tax_rate' => 6.00],
        ];

        foreach ($calibers as $caliber) {
            Caliber::firstOrCreate(['name' => $caliber['name']], $caliber);
        }

        // Get calibers for default assignment
        $caliber21 = Caliber::where('name', '21')->first();
        $caliber22 = Caliber::where('name', '22')->first();
        $caliber24 = Caliber::where('name', '24')->first();

        // Create default categories with their default calibers
        $categories = [
            ['name' => 'غوايش', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'سبايك', 'default_caliber_id' => $caliber24?->id],
            ['name' => 'طقم', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'نص طقم', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'حلق', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'دبلة', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'خاتم', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'سلسلة', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'تعليقة', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'كف', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'سبحة', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'سوارة', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'عقد', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'خلخال', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'تشكيلة', 'default_caliber_id' => $caliber21?->id],
            ['name' => 'جنية', 'default_caliber_id' => $caliber22?->id]
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['name' => $categoryData['name']],
                [
                    'default_caliber_id' => $categoryData['default_caliber_id'],
                    'is_active' => true
                ]
            );
        }

        // Create default expense types
        $expenseTypes = [
            'أجور مصنعية',
            'تسويق',
            'علب',
            'أخرى',
            'توصيل',
            'أكل',
            'الصين'
        ];

        foreach ($expenseTypes as $expenseTypeName) {
            ExpenseType::firstOrCreate(['name' => $expenseTypeName]);
        }

        // Create multiple branches for testing
        $branches = [
            [
                'name' => 'الفرع الرئيسي',
                'address' => 'شارع الجمهورية، وسط البلد',
                'phone' => '0123456789',
                'is_active' => true
            ],
            [
                'name' => 'فرع المعادي',
                'address' => 'شارع 9، المعادي',
                'phone' => '0223456789',
                'is_active' => true
            ],
            [
                'name' => 'فرع مدينة نصر',
                'address' => 'شارع عباس العقاد، مدينة نصر',
                'phone' => '0223456780',
                'is_active' => true
            ],
            [
                'name' => 'فرع الإسكندرية',
                'address' => 'شارع سعد زغلول، الإسكندرية',
                'phone' => '0333456789',
                'is_active' => true
            ],
            [
                'name' => 'فرع 6 أكتوبر',
                'address' => 'مول العرب، 6 أكتوبر',
                'phone' => '0238456789',
                'is_active' => true
            ]
        ];

        foreach ($branches as $branchData) {
            $branch = Branch::firstOrCreate(
                ['name' => $branchData['name']],
                $branchData
            );

            // Create 2-3 employees for each branch
            $employeeNames = [
                ['name' => 'أحمد محمد', 'salary' => 3000],
                ['name' => 'محمود علي', 'salary' => 3500],
                ['name' => 'سارة أحمد', 'salary' => 2800]
            ];

            foreach ($employeeNames as $index => $empData) {
                $email = 'employee_' . $branch->id . '_' . $index . '@example.com';
                Employee::firstOrCreate(
                    [
                        'name' => $empData['name'],
                        'branch_id' => $branch->id,
                        'email' => $email
                    ],
                    [
                        'phone' => '012' . rand(10000000, 99999999),
                        'salary' => $empData['salary'],
                        'is_active' => true
                    ]
                );
            }
        }
    }
}