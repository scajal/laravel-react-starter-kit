<?php

declare(strict_types=1);

use App\Modules\Core\Audit\Models\Activity;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use Illuminate\Support\Str;

afterEach(function (): void {
    Str::createRandomStringsNormally();
});

it('derives the slug from the name when none is given', function (): void {
    $team = Team::query()->create(['name' => 'Acme Corporation']);

    expect($team->slug)->toBe('acme-corporation');
});

it('lowercases a given slug', function (): void {
    $team = Team::query()->create(['name' => 'Acme', 'slug' => 'ACME-HQ']);

    expect($team->slug)->toBe('acme-hq');
});

it('appends a random suffix when the derived slug is taken', function (): void {
    Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
    Str::createRandomStringsUsingSequence(['AbCdEf']);

    $team = Team::query()->create(['name' => 'Acme']);

    expect($team->slug)->toBe('acme-abcdef');
});

it('keeps drawing suffixes until it finds a free slug', function (): void {
    Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
    Team::factory()->create(['name' => 'Acme', 'slug' => 'acme-aaaaaa']);
    Str::createRandomStringsUsingSequence(['aaaaaa', 'bbbbbb']);

    $team = Team::query()->create(['name' => 'Acme']);

    expect($team->slug)->toBe('acme-bbbbbb');
});

it('regenerates the slug when the name changes', function (): void {
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);

    $team->update(['name' => 'Globex']);

    expect($team->slug)->toBe('globex');
});

it('keeps its own slug when renamed to a name that derives the same slug', function (): void {
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);

    $team->update(['name' => 'ACME']);

    expect($team->slug)->toBe('acme');
});

it('keeps the slug when the name does not change', function (): void {
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme-hq']);

    $team->update(['is_active' => false]);

    expect($team->slug)->toBe('acme-hq');
});

it('lists its members with the role each one holds', function (): void {
    $team = Team::factory()->create();
    $owner = User::factory()->create();
    $member = User::factory()->create();
    Membership::factory()->owner()->for($team)->for($owner)->create();
    Membership::factory()->for($team)->for($member)->create();

    $roles = $team->users()->get()->mapWithKeys(fn (User $user): array => [$user->id => $user->membership?->role]);

    expect($roles->all())->toBe([
        $owner->id => Role::Owner,
        $member->id => Role::Member,
    ]);
});

it('logs only the attributes that changed', function (): void {
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);

    $team->update(['is_active' => false]);

    $activity = Activity::query()->forSubject($team)->forEvent('updated')->sole();
    expect($activity->attribute_changes?->get('attributes'))
        ->toMatchArray(['is_active' => false])
        ->not->toHaveKeys(['name', 'slug']);
});

it('does not log an update that changes nothing', function (): void {
    $team = Team::factory()->create(['name' => 'Acme']);

    $team->update(['name' => 'Acme']);

    expect(Activity::query()->forSubject($team)->forEvent('updated')->exists())->toBeFalse();
});

it('builds its timeline from its activity log', function (): void {
    $team = Team::factory()->create();

    $events = $team->timeline()->get()->pluck('event');

    expect($events->all())->toBe(['created']);
});
