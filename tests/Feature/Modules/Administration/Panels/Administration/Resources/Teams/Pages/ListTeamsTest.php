<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Teams\Pages\ListTeams;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

it('lists the teams', function (): void {
    asUser();
    $teams = Team::factory()->count(2)->create();

    $page = Livewire::test(ListTeams::class)->loadTable();

    $page->assertOk()->assertCanSeeTableRecords($teams);
});

it('searches the teams by name', function (): void {
    asUser();
    $acme = Team::factory()->create(['name' => 'Acme']);
    $globex = Team::factory()->create(['name' => 'Globex']);

    $page = Livewire::test(ListTeams::class)
        ->loadTable()
        ->searchTableColumns(['name' => 'acm']);

    $page->assertCanSeeTableRecords([$acme])->assertCanNotSeeTableRecords([$globex]);
});

it('filters the teams by their active state', function (): void {
    asUser();
    $active = Team::factory()->create();
    $inactive = Team::factory()->inactive()->create();

    $page = Livewire::test(ListTeams::class)
        ->loadTable()
        ->filterTable('is_active', false);

    $page->assertCanSeeTableRecords([$inactive])->assertCanNotSeeTableRecords([$active]);
});

it('labels each team as active or inactive', function (): void {
    asUser();
    $active = Team::factory()->create();
    $inactive = Team::factory()->inactive()->create();

    $page = Livewire::test(ListTeams::class)->loadTable();

    $page->assertTableColumnFormattedStateSet('is_active', 'Active', $active)
        ->assertTableColumnFormattedStateSet('is_active', 'Inactive', $inactive);
});

it('deletes a team along with its memberships', function (): void {
    asUser();
    $membership = Membership::factory()->create();

    $page = Livewire::test(ListTeams::class)
        ->loadTable()
        ->callAction(TestAction::make(DeleteAction::class)->table($membership->team));

    $page->assertNotified();
    $this->assertModelMissing($membership->team);
    $this->assertModelMissing($membership);
});
