<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAdminSession;
use App\Http\Middleware\DebugLoginRequest;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.session' => EnsureAdminSession::class,
        ]);
        $middleware->prependToGroup('web', DebugLoginRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
