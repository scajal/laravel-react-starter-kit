<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withExceptions(
        fn (Exceptions $exceptions): Exceptions => $exceptions
            ->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson())
            ->dontTruncateRequestExceptions()
    )
    ->withMiddleware(fn (Middleware $middleware): Middleware => $middleware
        ->encryptCookies()
        ->web(append: [
            \App\Modules\Support\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ])
    )
    ->withRouting(health: '/up', web: __DIR__.'/../routes/web.php')
    ->create();
