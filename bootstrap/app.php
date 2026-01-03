<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'trusted_device' => \App\Http\Middleware\TrustedDeviceCheck::class,
            'secret_device' => \App\Http\Middleware\SecretLinkDeviceRegister::class,
            'device_access' => \App\Http\Middleware\EnforceDeviceAccess::class,
            'block_login_unless_device_or_admin' => \App\Http\Middleware\BlockLoginUnlessDeviceOrAdmin::class,
            'device_valid' => \App\Http\Middleware\EnsureDeviceIsValid::class,
            'block_ip' => \App\Http\Middleware\BlockIpAfterFailedAttempts::class,
            'security_headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
