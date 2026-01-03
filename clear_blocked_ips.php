<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear all blocked IPs
$deleted = \App\Models\BlockedIp::query()->delete();

echo "Cleared {$deleted} blocked IP(s) from database.\n";
echo "All IPs are now unblocked!\n";
