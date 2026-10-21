<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Users\UserResource;
use Illuminate\Support\Facades\App;

test('model labels are translated and lowercase', function (string $locale, string $singular, string $plural): void {
    App::setLocale($locale);

    expect(UserResource::getModelLabel())->toBe($singular)
        ->and(UserResource::getPluralModelLabel())->toBe($plural);
})->with([
    'english' => ['en', 'user', 'users'],
    'spanish' => ['es', 'usuario', 'usuarios'],
]);

test('title case labels are translated and sentence case', function (string $locale, string $singular, string $plural): void {
    App::setLocale($locale);

    expect(UserResource::getTitleCaseModelLabel())->toBe($singular)
        ->and(UserResource::getTitleCasePluralModelLabel())->toBe($plural);
})->with([
    'english' => ['en', 'user', 'Users'],
    'spanish' => ['es', 'usuario', 'Usuarios'],
]);
