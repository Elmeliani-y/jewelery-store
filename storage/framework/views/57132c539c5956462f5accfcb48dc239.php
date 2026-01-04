<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $siteName = $settings['company_name'] ?? config('app.name', 'متجر المجوهرات');
    ?>
    <title>محظور | <?php echo e($siteName); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
        }
        .container {
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 10rem;
            font-weight: bold;
            color: #fff;
            text-align: center;
            line-height: 1;
        }
        .error-text {
            font-size: 3rem;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            margin-top: 2rem;
        }
        .error-message {
            font-size: 1.5rem;
            color: #fff;
            margin-top: 2rem;
            max-width: 800px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">403</div>
        <div class="error-text">محظور</div>
        <?php if(isset($message)): ?>
            <div class="error-message"><?php echo e($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/errors/403.blade.php ENDPATH**/ ?>