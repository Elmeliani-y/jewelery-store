<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sale;
use App\Models\Branch;
use App\Models\Employee;
use Carbon\Carbon;

echo "Seeding sales data...\n\n";

// Get branches and employees
$branches = Branch::all();
$employees = Employee::all();

if ($branches->isEmpty()) {
    echo "No branches found. Creating a branch...\n";
    $branch = Branch::create([
        'name' => 'الفرع الرئيسي',
        'is_active' => true,
    ]);
    $branches = collect([$branch]);
}

if ($employees->isEmpty()) {
    echo "No employees found. Creating an employee...\n";
    $employee = Employee::create([
        'name' => 'موظف المبيعات',
        'branch_id' => $branches->first()->id,
        'is_active' => true,
    ]);
    $employees = collect([$employee]);
}

// Create sales for today
$today = Carbon::today();
$salesData = [
    ['type' => 'cash', 'weight' => 25.5, 'amount' => 5000, 'payment' => 'cash'],
    ['type' => 'cash', 'weight' => 15.2, 'amount' => 3200, 'payment' => 'cash'],
    ['type' => 'cash', 'weight' => 30.0, 'amount' => 6500, 'payment' => 'network'],
    ['type' => 'installment', 'weight' => 45.5, 'amount' => 12000, 'payment' => 'cash'],
    ['type' => 'snap', 'weight' => 20.3, 'amount' => 4500, 'payment' => 'cash'],
];

foreach ($salesData as $data) {
    $branch = $branches->random();
    $employee = $employees->where('branch_id', $branch->id)->first() ?? $employees->random();
    
    $taxAmount = $data['amount'] * 0.10; // 10% tax
    $netAmount = $data['amount'] - $taxAmount;
    
    Sale::create([
        'invoice_number' => 'INV-' . strtoupper(uniqid()),
        'branch_id' => $branch->id,
        'employee_id' => $employee->id,
        'payment_method' => $data['payment'],
        'total_amount' => $data['amount'],
        'tax_amount' => $taxAmount,
        'net_amount' => $netAmount,
        'cash_amount' => $data['payment'] === 'cash' ? $data['amount'] : 0,
        'network_amount' => $data['payment'] === 'network' ? $data['amount'] : 0,
        'notes' => 'Sample sale',
        'is_returned' => false,
    ]);
    
    echo "Created sale: {$data['amount']} IQD ({$data['payment']})\n";
}

// Create some sales for yesterday
$yesterday = Carbon::yesterday();
for ($i = 0; $i < 3; $i++) {
    $branch = $branches->random();
    $employee = $employees->where('branch_id', $branch->id)->first() ?? $employees->random();
    
    $amount = rand(2000, 8000);
    $taxAmount = $amount * 0.10; // 10% tax
    $netAmount = $amount - $taxAmount;
    
    Sale::create([
        'invoice_number' => 'INV-' . strtoupper(uniqid()),
        'branch_id' => $branch->id,
        'employee_id' => $employee->id,
        'payment_method' => 'cash',
        'total_amount' => $amount,
        'tax_amount' => $taxAmount,
        'net_amount' => $netAmount,
        'cash_amount' => $amount,
        'network_amount' => 0,
        'notes' => 'Sample sale from yesterday',
        'is_returned' => false,
    ]);
}

echo "\nTotal sales created: " . Sale::count() . "\n";
echo "Done!\n";
