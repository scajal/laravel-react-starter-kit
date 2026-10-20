<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withExceptions(
        fn (Exceptions $exceptions): Exceptions => $exceptions
            ->dontTruncateRequestExceptions()
            ->shouldRenderJsonWhen(fn (Request $request): bool => $request->is('api/*') || $request->expectsJson())
    )
    ->withMiddleware(fn (Middleware $middleware): Middleware => $middleware
        ->encryptCookies(except: ['appearance', 'filament_language_switch_locale'])
        ->preventRequestForgery()
        ->web(append: [
            \App\Modules\Core\Identity\Http\Middleware\HandleAppearance::class,
            \App\Modules\Core\Identity\Http\Middleware\SetLocale::class,
            \App\Modules\Support\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ], prepend: [
            \Inertia\Middleware\EncryptHistory::class,
        ])
    )
    ->withRouting(web: __DIR__.'/../routes/web.php', health: '/up')
    ->create();
