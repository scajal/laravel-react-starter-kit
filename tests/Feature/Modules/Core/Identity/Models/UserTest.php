<?php

declare(strict_types=1);

use App\Modules\Core\Audit\Models\Activity;
use App\Modules\Core\Identity\Enums\Team\Permission;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Enums\User\PreferenceKey;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use App\Modules\Core\Identity\Models\User\Preference;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

it('belongs only to the teams it holds a membership of', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    Membership::factory()->for($user)->for($team)->create();

    expect($user->belongsToTeam($team))->toBeTrue()
        ->and($user->belongsToTeam($otherTeam))->toBeFalse();
});

it('exposes the role of each team through the membership pivot', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Membership::factory()->administrator()->for($user)->for($team)->create();

    $membership = $user->teams()->sole()->membership;

    expect($membership)->toBeInstanceOf(Membership::class)
        ->role->toBe(Role::Administrator);
});

it('returns the role the user holds on a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Membership::factory()->owner()->for($user)->for($team)->create();

    expect($user->teamRole($team))->toBe(Role::Owner);
});

it('has no membership or role on a team it does not belong to', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Membership::factory()->owner()->for($team)->create();

    expect($user->teamMembership($team))->toBeNull()
        ->and($user->teamRole($team))->toBeNull();
});

it('has the permissions its role grants on a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Membership::factory()->owner()->for($user)->for($team)->create();

    expect($user->hasTeamPermission($team, Permission::Wildcard))->toBeTrue();
});

it('has no permission on a team it does not belong to', function (): void {
    $user = User::factory()->create();
    $ownTeam = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    Membership::factory()->owner()->for($user)->for($ownTeam)->create();
    $user->switchTeam($ownTeam);

    expect($user->hasTeamPermission($otherTeam, Permission::Wildcard))->toBeFalse();
});

it('switches to a team it belongs to', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['slug' => 'acme']);
    Membership::factory()->for($user)->for($team)->create();

    $switched = $user->switchTeam($team);

    expect($switched)->toBeTrue()
        ->and($user->fresh()->isCurrentTeam($team))->toBeTrue()
        ->and(URL::getDefaultParameters())->toMatchArray(['team' => 'acme']);
});

it('refuses to switch to a team it does not belong to', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $switched = $user->switchTeam($team);

    expect($switched)->toBeFalse()
        ->and($user->fresh()->current_team_id)->toBeNull()
        ->and(URL::getDefaultParameters())->not->toHaveKey('team');
});

it('does not consider another team the current one', function (): void {
    $user = User::factory()->create();
    $currentTeam = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    Membership::factory()->for($user)->for($currentTeam)->create();
    $user->switchTeam($currentTeam);

    expect($user->isCurrentTeam($otherTeam))->toBeFalse();
});

it('returns null for a preference the user never stored', function (): void {
    $user = User::factory()->create();

    expect($user->preference(PreferenceKey::Appearance))->toBeNull();
});

it('replaces a stored preference instead of adding another', function (): void {
    $user = User::factory()->create();
    Preference::factory()->for($user)->appearance('dark')->create();

    $user->rememberPreference(PreferenceKey::Appearance, 'light');

    expect($user->preferences()->count())->toBe(1)
        ->and($user->preference(PreferenceKey::Appearance))->toBe('light');
});

it('prefers the stored locale for mail and notifications', function (): void {
    $user = User::factory()->create();
    Preference::factory()->for($user)->locale('es')->create();

    expect($user->preferredLocale())->toBe('es');
});

it('falls back to the current locale when none is stored', function (): void {
    $user = User::factory()->create();
    App::setLocale('es');

    expect($user->preferredLocale())->toBe('es');
});

it('holds no password since sign-in happens at WorkOS', function (): void {
    $user = User::factory()->make();

    expect($user->getAuthPassword())->toBe('');
});

it('can access the administration panel only in the local environment', function (string $environment, bool $canAccess): void {
    $user = User::factory()->make();
    App::detectEnvironment(fn (): string => $environment);

    expect($user->canAccessPanel(Filament::getPanel('administration')))->toBe($canAccess);
})->with([
    'local' => ['local', true],
    'production' => ['production', false],
    'testing' => ['testing', false],
]);

it('does not log the WorkOS identifier or the remember token', function (): void {
    $user = User::factory()->create();

    $user->update([
        'external_id' => 'user_new',
        'name' => 'Ada Lovelace',
        'remember_token' => 'secret-token',
    ]);

    $activity = Activity::query()->forSubject($user)->forEvent('updated')->sole();
    expect($activity->attribute_changes?->get('attributes'))
        ->toMatchArray(['name' => 'Ada Lovelace'])
        ->not->toHaveKeys(['external_id', 'remember_token']);
});

it('builds its timeline from its activity log', function (): void {
    $user = User::factory()->create();

    $events = $user->timeline()->get()->pluck('event');

    expect($events->all())->toBe(['created']);
});
