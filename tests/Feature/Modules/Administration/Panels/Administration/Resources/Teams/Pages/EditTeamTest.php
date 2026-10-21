<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Teams\Pages\EditTeam;
use App\Modules\Core\Identity\Models\Team;
use Livewire\Livewire;

it('renames a team and derives its new slug', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);

    $page = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->fillForm(['name' => 'Globex'])
        ->call('save');

    $page->assertHasNoFormErrors()->assertNotified();
    $this->assertDatabaseHas(Team::class, ['id' => $team->id, 'name' => 'Globex', 'slug' => 'globex']);
});

it('derives the slug from the name when it is cleared', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme-hq']);

    $page = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->fillForm(['slug' => null])
        ->call('save');

    $page->assertHasNoFormErrors()->assertNotified();
    $this->assertDatabaseHas(Team::class, ['id' => $team->id, 'slug' => 'acme']);
});

it('keeps the slug given along with a new name', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);

    $page = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->fillForm(['name' => 'Globex', 'slug' => 'globex-hq'])
        ->call('save');

    $page->assertHasNoFormErrors();
    $this->assertDatabaseHas(Team::class, ['id' => $team->id, 'name' => 'Globex', 'slug' => 'globex-hq']);
});

it('saves a team that keeps its own slug', function (): void {
    asUser();
    $team = Team::factory()->create(['slug' => 'acme']);

    $page = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])->call('save');

    $page->assertHasNoFormErrors();
});

it('deactivates an active team', function (): void {
    asUser();
    $team = Team::factory()->create();

    $page = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->assertActionHidden('activate')
        ->callAction('deactivate');

    $page->assertNotified('Saved');
    expect($team->fresh()?->is_active)->toBeFalse();
});

it('activates an inactive team', function (): void {
    asUser();
    $team = Team::factory()->inactive()->create();

    $page = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->assertActionHidden('deactivate')
        ->callAction('activate');

    $page->assertNotified('Saved');
    expect($team->fresh()?->is_active)->toBeTrue();
});
