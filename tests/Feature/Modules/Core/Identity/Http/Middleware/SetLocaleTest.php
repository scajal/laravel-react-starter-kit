<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Http\Middleware\SetLocale;
use App\Modules\Core\Identity\Models\User;
use App\Modules\Core\Identity\Models\User\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::middleware('web')->get('/_test/locale/{locale?}', fn (): string => App::currentLocale());
});

it('uses the locale in the route segment', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->withUnencryptedCookie('filament_language_switch_locale', 'en')
        ->get('/_test/locale/es');

    $response->assertOk()
        ->assertContent('es')
        ->assertCookie('filament_language_switch_locale', 'es', encrypted: false)
        ->assertSessionHas('locale', 'es');
});

it('skips a route segment locale that is not installed', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->withUnencryptedCookie('filament_language_switch_locale', 'es')
        ->get('/_test/locale/fr');

    $response->assertOk()
        ->assertContent('es')
        ->assertSessionHas('locale', 'es');
});

it('skips a stored locale that is no longer installed', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    Preference::factory()->for($user)->locale('fr')->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('filament_language_switch_locale', 'es')
        ->get('/_test/locale');

    $response->assertOk()->assertContent('es');
});

it('uses the stored locale of a member over the cookie', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    Preference::factory()->for($user)->locale('es')->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('filament_language_switch_locale', 'en')
        ->get('/_test/locale');

    $response->assertOk()
        ->assertContent('es')
        ->assertCookie('filament_language_switch_locale', 'es', encrypted: false);
});

it('stores the cookie locale for a member who never chose one', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('filament_language_switch_locale', 'es')
        ->get('/_test/locale');

    $response->assertOk()->assertContent('es');
    $this->assertDatabaseHas(Preference::class, [
        'user_id' => $user->id,
        'key' => 'locale',
        'value' => 'es',
    ]);
});

it('does not store a cookie locale that is not installed', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('filament_language_switch_locale', 'fr')
        ->get('/_test/locale');

    $response->assertOk()->assertContent('en');
    $this->assertDatabaseEmpty(Preference::class);
});

it('uses the cookie locale of a guest', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->withUnencryptedCookie('filament_language_switch_locale', 'es')
        ->get('/_test/locale');

    $response->assertOk()->assertContent('es');
});

it('uses the preferred language of the browser', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->withHeader('Accept-Language', 'es-ES,es;q=0.9,en;q=0.8')
        ->get('/_test/locale');

    $response->assertOk()
        ->assertContent('es')
        ->assertCookie('filament_language_switch_locale', 'es', encrypted: false);
});

it('uses the preferred language of the browser over a cookie locale that is not installed', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->withUnencryptedCookie('filament_language_switch_locale', 'fr')
        ->withHeader('Accept-Language', 'es')
        ->get('/_test/locale');

    $response->assertOk()->assertContent('es');
});

it('falls back to the default locale for a client that sends no language preference', function (): void {
    config(['app.locale' => 'es']);
    $request = Request::create('/');
    $request->headers->remove('Accept-Language');

    $locale = new SetLocale()->resolve($request);

    expect($locale)->toBe('es');
});
