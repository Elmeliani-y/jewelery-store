<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$expense = App\Models\Expense::find(52);
$user = App\Models\User::find(3);

echo "Expense 52:\n";
echo "  branch_id: " . $expense->branch_id . "\n";
echo "  branch_id type: " . gettype($expense->branch_id) . "\n";

echo "\nUser 3:\n";
echo "  branch_id: " . $user->branch_id . "\n";
echo "  branch_id type: " . gettype($user->branch_id) . "\n";
echo "  isBranch(): " . ($user->isBranch() ? 'true' : 'false') . "\n";

echo "\nComparison:\n";
echo "  \$expense->branch_id != \$user->branch_id: " . ($expense->branch_id != $user->branch_id ? 'true' : 'false') . "\n";
echo "  \$user->isBranch() && \$expense->branch_id != \$user->branch_id: " . ($user->isBranch() && $expense->branch_id != $user->branch_id ? 'true (403)' : 'false (OK)') . "\n";
