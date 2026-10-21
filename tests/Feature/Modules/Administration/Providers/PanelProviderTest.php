<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\User\Preference;
use BezhanSalleh\LanguageSwitch\Events\LocaleChanged;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

it('redirects a guest to the login page', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/administration');

    $response->assertRedirectToRoute('login');
});

it('forbids the administration outside the local environment', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();

    $response = $this->get('/administration');

    $response->assertForbidden();
});

it('offers the installed locales in the language switch', function (): void {
    $locales = LanguageSwitch::make()->getLocales();

    expect($locales)->toEqualCanonicalizing(['en', 'es']);
});

it('prefers the locale cookie in the language switch', function (): void {
    request()->cookies->set('filament_language_switch_locale', 'es');

    $locale = LanguageSwitch::make()->getUserPreferredLocale();

    expect($locale)->toBe('es');
});

it('stores the locale a member picks in the language switch', function (): void {
    /** @var \Tests\TestCase $this */
    $user = asUser();

    event(new LocaleChanged('es'));

    $this->assertDatabaseHas(Preference::class, [
        'user_id' => $user->id,
        'key' => 'locale',
        'value' => 'es',
    ]);
});

it('does not store a locale that is not installed', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();

    event(new LocaleChanged('fr'));

    $this->assertDatabaseEmpty(Preference::class);
});

it('does not store a locale for a guest', function (): void {
    /** @var \Tests\TestCase $this */
    event(new LocaleChanged('es'));

    $this->assertDatabaseEmpty(Preference::class);
});
