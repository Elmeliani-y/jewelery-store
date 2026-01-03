<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Users in database:\n";
echo "==================\n\n";

$users = \App\Models\User::all();
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Username: {$user->username}\n";
    echo "Role: {$user->role}\n";
    echo "Branch ID: {$user->branch_id}\n";
    echo "Created: {$user->created_at}\n";
    echo "---\n";
}
