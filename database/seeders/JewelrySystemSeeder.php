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

        // Create default categories
        $categories = [
            'غوايش',
            'سبايك', 
            'طقم',
            'نص طقم',
            'حلق',
            'دبلة',
            'خاتم',
            'سلسلة',
            'تعليقة',
            'كف',
            'سبحة',
            'سوارة',
            'عقد',
            'خلخال',
            'تشكيلة',
            'جنية'
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
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
                Employee::firstOrCreate(
                    ['email' => strtolower(str_replace(' ', '', $empData['name'])) . $branch->id . '@jewelry.com'],
                    [
                        'name' => $empData['name'],
                        'phone' => '012' . rand(10000000, 99999999),
                        'salary' => $empData['salary'],
                        'branch_id' => $branch->id,
                        'is_active' => true
                    ]
                );
            }
        }
    }
}