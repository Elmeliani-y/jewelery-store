<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Activate admin device
$device = \App\Models\Device::where('token', 'admin-static')->first();
if ($device) {
    $device->active = true;
    $device->user_id = 2;
    $device->save();
    echo "Admin device activated: ID {$device->id}\n";
} else {
    $device = \App\Models\Device::create([
        'name' => 'admin',
        'token' => 'admin-static',
        'user_id' => 2,
        'active' => true,
        'last_login_at' => now(),
    ]);
    echo "Admin device created: ID {$device->id}\n";
}

echo "Device status:\n";
echo "ID: {$device->id}\n";
echo "Token: {$device->token}\n";
echo "User ID: {$device->user_id}\n";
echo "Active: " . ($device->active ? 'Yes' : 'No') . "\n";
