<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\User;
use App\Modules\Core\Identity\Models\User\Preference;
use Inertia\Testing\AssertableInertia;

it('shares the signed-in user without their secrets', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
        ->component('welcome')
        ->where('user.id', $user->id)
        ->where('user.email', $user->email)
        ->missing('user.external_id')
        ->missing('user.remember_token'));
});

it('shares the colour scheme and the locale of the member', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    Preference::factory()->for($user)->appearance('dark')->create();
    Preference::factory()->for($user)->locale('es')->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
        ->where('appearance', 'dark')
        ->where('locale', 'es'));
});

it('shares that the administration is closed outside the local environment', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
        ->where('authorization.canAccessAdministration', false)
        ->where('authorization.teamPermissions', []));
});
