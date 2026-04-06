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
    ->withMiddleware(function (Middleware $middleware): void {
        // Disable CSRF for all trap pages — attackers/bots won't send CSRF tokens
        // The dashboard is read-only so it needs no CSRF protection either
        $middleware->validateCsrfTokens(except: ['*']);
        $middleware->append(\App\Http\Middleware\LogHoneypotRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
