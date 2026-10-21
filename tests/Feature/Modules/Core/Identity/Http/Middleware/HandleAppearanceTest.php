<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\User;
use App\Modules\Core\Identity\Models\User\Preference;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

beforeEach(function (): void {
    Route::middleware('web')->get('/_test/appearance', fn (): mixed => View::shared('appearance'));
});

it('shares the colour scheme a guest chose in the cookie', function (?string $cookie, string $appearance): void {
    /** @var \Tests\TestCase $this */
    $request = $cookie === null ? $this : $this->withUnencryptedCookie('appearance', $cookie);

    $response = $request->get('/_test/appearance');

    $response->assertOk()->assertContent($appearance);
})->with([
    'dark' => ['dark', 'dark'],
    'light' => ['light', 'light'],
    'system' => ['system', 'system'],
    'an unknown scheme' => ['sepia', 'system'],
    'no cookie' => [null, 'system'],
]);

it('shares the stored scheme over the cookie and rewrites the cookie', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    Preference::factory()->for($user)->appearance('light')->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('appearance', 'dark')
        ->get('/_test/appearance');

    $response->assertOk()
        ->assertContent('light')
        ->assertCookie('appearance', 'light', encrypted: false);
});

it('stores the cookie scheme for a member who never chose one', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('appearance', 'dark')
        ->get('/_test/appearance');

    $response->assertOk()->assertContent('dark');
    $this->assertDatabaseHas(Preference::class, [
        'user_id' => $user->id,
        'key' => 'appearance',
        'value' => 'dark',
    ]);
});

it('does not store an unknown cookie scheme', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withUnencryptedCookie('appearance', 'sepia')
        ->get('/_test/appearance');

    $response->assertOk()
        ->assertContent('system')
        ->assertCookieMissing('appearance');
    $this->assertDatabaseEmpty(Preference::class);
});
