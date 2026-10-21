<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Teams\Pages\ViewTeam;
use App\Modules\Administration\Panels\Administration\Resources\Teams\RelationManagers\MembersRelationManager;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use Livewire\Livewire;

it('shows the team with its members', function (): void {
    asUser();
    $team = Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);

    $page = Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()]);

    $page->assertOk()
        ->assertSchemaStateSet(['name' => 'Acme', 'slug' => 'acme'])
        ->assertSeeLivewire(MembersRelationManager::class);
});

it('deactivates an active team', function (): void {
    asUser();
    $team = Team::factory()->create();

    $page = Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()])
        ->assertActionHidden('activate')
        ->callAction('deactivate');

    $page->assertNotified('Saved');
    expect($team->fresh()?->is_active)->toBeFalse();
});

it('activates an inactive team', function (): void {
    asUser();
    $team = Team::factory()->inactive()->create();

    $page = Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()])
        ->assertActionHidden('deactivate')
        ->callAction('activate');

    $page->assertNotified('Saved');
    expect($team->fresh()?->is_active)->toBeTrue();
});

it('shows the activity log of the team', function (): void {
    asUser(User::factory()->create(['name' => 'Ada Lovelace']));
    $team = Team::factory()->create(['name' => 'Acme']);
    $team->update(['name' => 'Globex']);

    $page = Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()])
        ->mountAction('activityLog');

    $page->assertMountedActionModalSee(['Ada Lovelace', '2 attributes', 'Acme', 'Globex']);
});
