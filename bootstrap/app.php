<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

putenv('SESSION_DRIVER=file');
$_ENV['SESSION_DRIVER'] = 'file';
$_SERVER['SESSION_DRIVER'] = 'file';

putenv('CACHE_STORE=file');
$_ENV['CACHE_STORE'] = 'file';
$_SERVER['CACHE_STORE'] = 'file';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
