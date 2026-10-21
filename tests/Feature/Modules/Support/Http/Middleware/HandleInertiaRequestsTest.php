<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
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
        ->where('authorization.can_access_administration', false)
        ->where('authorization.team_permissions', []));
});

it('shares the team the member is working in', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Membership::factory()->for($team)->for($user)->create(['role' => Role::Owner]);
    $user->switchTeam($team);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
        ->where('current_team.id', $team->id)
        ->where('current_team.slug', $team->slug)
        ->where('authorization.team_permissions', ['*']));
});

it('shares no team when the member is not working in one', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
        ->where('current_team', null));
});

it('shares the user on partial reloads that do not ask for them', function (): void {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
        ->reloadOnly('locale', fn (AssertableInertia $reload): AssertableInertia => $reload
            ->where('user.id', $user->id)
            ->missing('appearance')
            ->missing('authorization')
            ->missing('current_team')));
});
