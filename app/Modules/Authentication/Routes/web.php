<?php

declare(strict_types=1);

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| This file holds all the routes for the authentication module.
|
*/

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
|
| Here is where you can register guest routes for your application. These
| routes are for users who are not authenticated.
|
*/

Route::middleware(RedirectIfAuthenticated::class)->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Authentication routes
    |--------------------------------------------------------------------------
    |
    | This routes are used to authenticate the user.
    |
    */

    Route::get('login', [\App\Modules\Authentication\Http\Controllers\AuthenticationController::class, 'login'])
        ->name('login');

    Route::get('authenticate', [\App\Modules\Authentication\Http\Controllers\AuthenticationController::class, 'authenticate'])
        ->name('authenticate');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
|
| Here is where you can register authenticated routes for your application. These
| routes are for users who are authenticated.
|
*/

Route::middleware([Authenticate::class, ValidateSessionWithWorkOS::class])->group(function (): void {
    Route::get('logout', [\App\Modules\Authentication\Http\Controllers\AuthenticationController::class, 'logout'])
        ->name('logout');
});
