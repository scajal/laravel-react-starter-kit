<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

Route::get('/', fn () => inertia('welcome'))
    ->middleware([Authenticate::class, ValidateSessionWithWorkOS::class]);

/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
|
| This file contains the routes for the authentication module.
|
*/

require __DIR__.'/../app/Modules/Authentication/Routes/web.php';
