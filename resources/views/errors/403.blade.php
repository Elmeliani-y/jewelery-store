<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $siteName = $settings['company_name'] ?? config('app.name', 'متجر المجوهرات');
    @endphp
    <title>محظور | {{ $siteName }}</title>
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
    </style>
</head>
<body>
    <div>
        <div class="error-code">403</div>
        <div class="error-text">محظور</div>
    </div>
</body>
</html>
