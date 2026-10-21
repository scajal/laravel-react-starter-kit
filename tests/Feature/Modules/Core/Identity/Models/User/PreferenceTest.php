<?php

declare(strict_types=1);

use App\Modules\Core\Audit\Models\Activity;
use App\Modules\Core\Identity\Models\User;
use App\Modules\Core\Identity\Models\User\Preference;

it('belongs to the user who stored it', function (): void {
    $user = User::factory()->create();

    $preference = Preference::factory()->for($user)->create();

    expect($preference->user->is($user))->toBeTrue();
});

it('logs a change of value', function (): void {
    $preference = Preference::factory()->appearance('dark')->create();

    $preference->update(['value' => 'light']);

    $activity = Activity::query()->forSubject($preference)->forEvent('updated')->sole();
    expect($activity->attribute_changes?->get('attributes'))->toMatchArray(['value' => 'light'])
        ->and($activity->attribute_changes?->get('old'))->toMatchArray(['value' => 'dark']);
});

it('builds its timeline from its activity log', function (): void {
    $preference = Preference::factory()->create();

    $events = $preference->timeline()->get()->pluck('event');

    expect($events->all())->toBe(['created']);
});
