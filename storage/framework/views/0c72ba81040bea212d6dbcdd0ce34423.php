<meta charset="utf-8" />
<?php
	$settings = \App\Models\Setting::all()->pluck('value', 'key');
	$siteName = $settings['company_name'] ?? config('app.name', 'متجر المجوهرات');
	$siteDesc = $settings['meta_description'] ?? 'متجر المجوهرات';
	$siteAuthor = $settings['meta_author'] ?? 'Jewelry Store';
	$favicon = $settings['logo_path'] ?? '/images/favicon.ico';
?>
<title><?php echo e(($title ?? $siteName)); ?> | <?php echo e($siteName); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo e($siteDesc); ?>" />
<meta name="author" content="<?php echo e($siteAuthor); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- App favicon -->
<link rel="shortcut icon" href="<?php echo e(asset($favicon)); ?>"><?php /**PATH C:\Dusty_Laravel_v1.0.0\Dusty\resources\views/layouts/partials/title-meta.blade.php ENDPATH**/ ?>