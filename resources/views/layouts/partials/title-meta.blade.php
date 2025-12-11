<meta charset="utf-8" />
@php
	$settings = \App\Models\Setting::all()->pluck('value', 'key');
	$siteName = $settings['company_name'] ?? config('app.name', 'متجر المجوهرات');
	$siteDesc = $settings['meta_description'] ?? 'متجر المجوهرات';
	$siteAuthor = $settings['meta_author'] ?? 'Jewelry Store';
	$favicon = $settings['logo_path'] ?? '/images/favicon.ico';
@endphp
<title>{{ ($title ?? $siteName) }} | {{ $siteName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ $siteDesc }}" />
<meta name="author" content="{{ $siteAuthor }}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset($favicon) }}">