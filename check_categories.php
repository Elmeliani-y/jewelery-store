<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$categories = App\Models\Category::with('defaultCaliber')->get();

echo "الأصناف والعيارات الافتراضية:\n";
echo str_repeat('=', 50) . "\n";

foreach($categories as $cat) {
    echo sprintf(
        "%-20s => %s\n",
        $cat->name,
        $cat->defaultCaliber ? 'عيار ' . $cat->defaultCaliber->name : 'لا يوجد'
    );
}
