<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\User\PreferenceKey;
use Illuminate\Support\Facades\App;

test('each preference has a translated label', function (PreferenceKey $key, string $english, string $spanish): void {
    expect($key->getLabel())->toBe($english);

    App::setLocale('es');

    expect($key->getLabel())->toBe($spanish);
})->with([
    'appearance' => [PreferenceKey::Appearance, 'Theme', 'Tema'],
    'locale' => [PreferenceKey::Locale, 'Language', 'Idioma'],
]);

test('the appearance accepts the colour schemes the menus offer', function (): void {
    expect(PreferenceKey::Appearance->values())->toBe(['light', 'dark', 'system']);
});

test('the locale accepts the installed locales', function (): void {
    expect(PreferenceKey::Locale->values())->toEqualCanonicalizing(['en', 'es']);
});

test('a preference accepts only the values it offers', function (PreferenceKey $key, mixed $value, bool $accepted): void {
    expect($key->accepts($value))->toBe($accepted);
})->with([
    'a colour scheme' => [PreferenceKey::Appearance, 'dark', true],
    'an unknown colour scheme' => [PreferenceKey::Appearance, 'sepia', false],
    'an installed locale' => [PreferenceKey::Locale, 'es', true],
    'a locale that is not installed' => [PreferenceKey::Locale, 'fr', false],
    'a value of another preference' => [PreferenceKey::Locale, 'dark', false],
    'a non-string value' => [PreferenceKey::Appearance, ['dark'], false],
    'no value' => [PreferenceKey::Appearance, null, false],
]);

test('each preference is mirrored by the cookie the client reads', function (PreferenceKey $key, string $cookieName): void {
    expect($key->cookieName())->toBe($cookieName);
})->with([
    'appearance' => [PreferenceKey::Appearance, 'appearance'],
    'locale' => [PreferenceKey::Locale, 'filament_language_switch_locale'],
]);

test('the cookie lasts a year and stays readable by the browser', function (): void {
    /** @var \Tests\TestCase $this */
    $this->freezeTime();

    $cookie = PreferenceKey::Appearance->cookie('dark');

    expect($cookie)
        ->getName()->toBe('appearance')
        ->getValue()->toBe('dark')
        ->isHttpOnly()->toBeFalse()
        ->getExpiresTime()->toBe(now()->addYear()->getTimestamp());
});
