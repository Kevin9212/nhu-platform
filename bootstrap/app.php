<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckBanned;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 👇 把 CORS middleware 掛到 api 群組
        $middleware->appendToGroup('api', HandleCors::class);

        // 👇 供 routes/web.php 使用的別名 middleware
        $middleware->alias([    
            'admin' => AdminMiddle::class,
            'checkBanned' => CheckBanned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    // 👇 這一行非常重要：一定要呼叫 create()，才會回傳 Application，而不是 ApplicationBuilder
    ->create();