<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <?php
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $siteName = $settings['company_name'] ?? config('app.name', 'متجر المجوهرات');
    ?>
    <title>مرحباً | <?php echo e($siteName); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }
    </style>
</head>
<body>
    <!-- Blank white page -->
</body>
</html>
<?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/landing.blade.php ENDPATH**/ ?>