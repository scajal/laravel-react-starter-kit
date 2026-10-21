<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\Team;

it('activates an inactive model', function (): void {
    $team = Team::factory()->inactive()->create();

    $team->activate();

    expect($team->fresh()?->is_active)->toBeTrue();
});

it('deactivates an active model', function (): void {
    $team = Team::factory()->create();

    $team->deactivate();

    expect($team->fresh()?->is_active)->toBeFalse();
});

it('scopes the query to active models by default', function (): void {
    $active = Team::factory()->create();
    Team::factory()->inactive()->create();

    $teams = Team::query()->isActive()->get();

    expect($teams->modelKeys())->toBe([$active->id]);
});

it('scopes the query by the active state given as a string', function (string $value, bool $expectsActive): void {
    $active = Team::factory()->create();
    $inactive = Team::factory()->inactive()->create();

    $teams = Team::query()->isActive($value)->get();

    expect($teams->modelKeys())->toBe([$expectsActive ? $active->id : $inactive->id]);
})->with([
    'true' => ['true', true],
    '1' => ['1', true],
    'false' => ['false', false],
    '0' => ['0', false],
]);
