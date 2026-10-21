<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Teams\Pages\ViewTeam;
use App\Modules\Administration\Panels\Administration\Resources\Teams\RelationManagers\MembersRelationManager;
use App\Modules\Administration\Panels\Administration\Resources\Users\UserResource;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

it('lists the members of the team with their role', function (): void {
    asUser();
    $team = Team::factory()->create();
    $owner = Membership::factory()->owner()->for($team)->create()->user;
    $outsider = Membership::factory()->create()->user;

    $page = Livewire::test(MembersRelationManager::class, ['ownerRecord' => $team, 'pageClass' => ViewTeam::class])->loadTable();

    $page->assertOk()
        ->assertCanSeeTableRecords([$owner])
        ->assertCanNotSeeTableRecords([$outsider])
        ->assertTableColumnStateSet('membership.role', Role::Owner, $owner);
});

it('attaches a user with the chosen role and makes it their current team', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $team = Team::factory()->create();
    $user = User::factory()->create();

    $page = Livewire::test(MembersRelationManager::class, ['ownerRecord' => $team, 'pageClass' => ViewTeam::class])
        ->loadTable()
        ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $user->id, 'role' => Role::Administrator->value]);

    $page->assertHasNoFormErrors();
    $this->assertDatabaseHas(Membership::class, ['team_id' => $team->id, 'user_id' => $user->id, 'role' => 'admin']);
    expect($user->fresh()?->current_team_id)->toBe($team->id);
});

it('keeps the current team of a user who already has one', function (): void {
    asUser();
    $team = Team::factory()->create();
    $membership = Membership::factory()->create();
    $membership->user->switchTeam($membership->team);

    Livewire::test(MembersRelationManager::class, ['ownerRecord' => $team, 'pageClass' => ViewTeam::class])
        ->loadTable()
        ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $membership->user->id, 'role' => Role::Member->value]);

    expect($membership->user->fresh()?->current_team_id)->toBe($membership->team->id);
});

it('requires a role to attach a user', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $team = Team::factory()->create();
    $user = User::factory()->create();

    $page = Livewire::test(MembersRelationManager::class, ['ownerRecord' => $team, 'pageClass' => ViewTeam::class])
        ->loadTable()
        ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $user->id, 'role' => null]);

    $page->assertHasFormErrors(['role' => 'required']);
    $this->assertDatabaseEmpty(Membership::class);
});

it('detaches a member from the team', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $membership = Membership::factory()->create();

    Livewire::test(MembersRelationManager::class, ['ownerRecord' => $membership->team, 'pageClass' => ViewTeam::class])
        ->loadTable()
        ->callAction(TestAction::make(DetachAction::class)->table($membership->user));

    $this->assertModelMissing($membership);
    $this->assertModelExists($membership->user);
});

it('links each member to their user page', function (): void {
    asUser();
    $membership = Membership::factory()->create();

    $page = Livewire::test(MembersRelationManager::class, ['ownerRecord' => $membership->team, 'pageClass' => ViewTeam::class])->loadTable();

    $page->assertActionHasUrl(
        TestAction::make('view')->table($membership->user),
        UserResource::getUrl('view', ['record' => $membership->user]),
    );
});
