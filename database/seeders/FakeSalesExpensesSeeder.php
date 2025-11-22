<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Caliber;
use App\Models\ExpenseType;
use Carbon\Carbon;

class FakeSalesExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        $categories = Category::all();
        $calibers = Caliber::all();
        $expenseTypes = ExpenseType::all();

        if ($branches->isEmpty() || $categories->isEmpty() || $calibers->isEmpty()) {
            $this->command->error('Please run JewelrySystemSeeder first!');
            return;
        }

        // Generate sales for the last 12 months
        $startDate = Carbon::now()->subMonths(12);
        
        $this->command->info('Creating fake sales data...');
        
        $totalDays = 365;
        for ($i = 0; $i < $totalDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $salesPerDay = rand(5, 15);
            
            for ($j = 0; $j < $salesPerDay; $j++) {
                $branch = $branches->random();
                $employee = Employee::where('branch_id', $branch->id)->inRandomOrder()->first();
                $category = $categories->random();
                $caliber = $calibers->random();
                
                $weight = rand(5, 200) / 10; // 0.5 to 20 grams
                $totalAmount = rand(500, 5000);
                $taxAmount = $totalAmount * ($caliber->tax_rate / 100);
                $netAmount = $totalAmount - $taxAmount;
                
                $paymentMethod = ['cash', 'network', 'mixed'][rand(0, 2)];
                $cashAmount = 0;
                $networkAmount = 0;
                
                if ($paymentMethod == 'cash') {
                    $cashAmount = $totalAmount;
                } elseif ($paymentMethod == 'network') {
                    $networkAmount = $totalAmount;
                } else {
                    $cashAmount = $totalAmount / 2;
                    $networkAmount = $totalAmount / 2;
                }
                
                Sale::create([
                    'branch_id' => $branch->id,
                    'employee_id' => $employee->id,
                    'category_id' => $category->id,
                    'caliber_id' => $caliber->id,
                    'invoice_number' => 'INV-' . $date->format('Ymd') . '-' . uniqid(),
                    'weight' => $weight,
                    'total_amount' => $totalAmount,
                    'cash_amount' => $cashAmount,
                    'network_amount' => $networkAmount,
                    'network_reference' => $paymentMethod != 'cash' ? 'REF-' . rand(100000, 999999) : null,
                    'payment_method' => $paymentMethod,
                    'tax_amount' => $taxAmount,
                    'net_amount' => $netAmount,
                    'is_returned' => rand(0, 20) == 0, // 5% return rate
                    'notes' => rand(0, 3) == 0 ? 'عملية بيع - ' . $this->getRandomName() : null,
                    'created_at' => $date->setTime(rand(9, 20), rand(0, 59)),
                    'updated_at' => $date->setTime(rand(9, 20), rand(0, 59)),
                ]);
            }
        }
        
        $this->command->info('Creating fake expenses data...');
        
        // Generate expenses for the last 12 months
        for ($i = 0; $i < $totalDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $expensesPerDay = rand(2, 5);
            
            for ($j = 0; $j < $expensesPerDay; $j++) {
                $branch = $branches->random();
                $expenseType = $expenseTypes->random();
                
                $amount = rand(100, 5000);
                
                Expense::create([
                    'branch_id' => $branch->id,
                    'expense_type_id' => $expenseType->id,
                    'amount' => $amount,
                    'description' => $this->getRandomExpenseDescription($expenseType->name),
                    'expense_date' => $date->format('Y-m-d'),
                    'notes' => rand(0, 3) == 0 ? 'ملاحظات إضافية' : null,
                    'created_at' => $date->setTime(rand(9, 20), rand(0, 59)),
                    'updated_at' => $date->setTime(rand(9, 20), rand(0, 59)),
                ]);
            }
        }
        
        $this->command->info('Fake data created successfully!');
        $this->command->info('Sales: ' . Sale::count());
        $this->command->info('Expenses: ' . Expense::count());
    }
    
    private function getRandomName()
    {
        $names = [
            'أحمد محمد',
            'محمد عبدالله',
            'فاطمة أحمد',
            'خالد عبدالرحمن',
            'نورة سعد',
            'سارة عبدالعزيز',
            'عبدالله خالد',
            'ريم محمد',
            'سعود أحمد',
            'مها عبدالرحمن',
            'يوسف علي',
            'هند سعيد',
            'عمر حسن',
            'لمى محمود',
            'فهد سلطان'
        ];
        
        return $names[array_rand($names)];
    }
    
    private function getRandomExpenseDescription($expenseType)
    {
        $descriptions = [
            'رواتب' => ['راتب شهر نوفمبر', 'رواتب الموظفين', 'بدلات إضافية'],
            'إيجار' => ['إيجار المحل', 'إيجار شهر نوفمبر', 'دفعة الإيجار الشهرية'],
            'كهرباء' => ['فاتورة الكهرباء', 'كهرباء شهر أكتوبر', 'فاتورة الطاقة'],
            'صيانة' => ['صيانة الأجهزة', 'إصلاح التكييف', 'تجديد الديكور'],
            'مشتريات' => ['شراء ذهب خام', 'مواد خام', 'مستلزمات المحل'],
        ];
        
        $typeDescriptions = $descriptions[$expenseType] ?? ['مصروف متنوع'];
        return $typeDescriptions[array_rand($typeDescriptions)];
    }
}
