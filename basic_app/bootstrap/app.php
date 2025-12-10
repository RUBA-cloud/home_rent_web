<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\JWTAuthMiddleware; // Import your custom middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // --- Route middleware aliases (use in routes) ---
        $middleware->alias([
            'module' => \App\Http\Middleware\EnsureModuleEnabled::class,
            'perm'   => \App\Http\Middleware\EnsurePermission::class,
        ]);
                // Add your JWT middleware here

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
