<?php

declare(strict_types=1);

use App\Modules\Core\Audit\Models\Activity;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;

it('connects a user to a team', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();

    $membership = Membership::factory()->for($team)->for($user)->create();

    expect($membership->team->is($team))->toBeTrue()
        ->and($membership->user->is($user))->toBeTrue();
});

it('logs a change of role', function (): void {
    $membership = Membership::factory()->create();

    $membership->update(['role' => Role::Administrator]);

    $activity = Activity::query()->forSubject($membership)->forEvent('updated')->sole();
    expect($activity->attribute_changes?->get('attributes'))->toMatchArray(['role' => 'admin'])
        ->and($activity->attribute_changes?->get('old'))->toMatchArray(['role' => 'member']);
});

it('builds its timeline from its activity log', function (): void {
    $membership = Membership::factory()->create();

    $events = $membership->timeline()->get()->pluck('event');

    expect($events->all())->toBe(['created']);
});
