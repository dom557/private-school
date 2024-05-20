<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Create the application instance
$app = Application::configure(basePath: dirname(__DIR__))

    // Register web routes and console commands
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Register middleware
    ->withMiddleware(function (Middleware $middleware) {
        // Add your middleware here
    })

    // Register exception handlers
    ->withExceptions(function (Exceptions $exceptions) {
        // Add your exception handlers here
    })

    // Register custom console commands
    ->withCommands([
        \App\Console\Commands\SchoolManager::class,
    ])

    // Finalize the application creation
    ->create();

return $app;
