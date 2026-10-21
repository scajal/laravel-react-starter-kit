<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Teams\TeamResource;
use App\Modules\Administration\Panels\Administration\Resources\Users\Pages\ViewUser;
use App\Modules\Administration\Panels\Administration\Resources\Users\RelationManagers\TeamsRelationManager;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

it('lists the teams of the user with their role', function (): void {
    asUser();
    $user = User::factory()->create();
    $team = Membership::factory()->administrator()->for($user)->create()->team;
    $otherTeam = Membership::factory()->create()->team;

    $page = Livewire::test(TeamsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => ViewUser::class])->loadTable();

    $page->assertOk()
        ->assertCanSeeTableRecords([$team])
        ->assertCanNotSeeTableRecords([$otherTeam])
        ->assertTableColumnStateSet('membership.role', Role::Administrator, $team);
});

it('attaches a team with the chosen role and makes it the current team', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $page = Livewire::test(TeamsRelationManager::class, ['ownerRecord' => $user, 'pageClass' => ViewUser::class])
        ->loadTable()
        ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $team->id, 'role' => Role::Owner->value]);

    $page->assertHasNoFormErrors();
    $this->assertDatabaseHas(Membership::class, ['team_id' => $team->id, 'user_id' => $user->id, 'role' => 'owner']);
    expect($user->fresh()?->current_team_id)->toBe($team->id);
});

it('keeps the current team of a user who already has one', function (): void {
    asUser();
    $membership = Membership::factory()->create();
    $membership->user->switchTeam($membership->team);
    $team = Team::factory()->create();

    Livewire::test(TeamsRelationManager::class, ['ownerRecord' => $membership->user, 'pageClass' => ViewUser::class])
        ->loadTable()
        ->callAction(TestAction::make(AttachAction::class)->table(), ['recordId' => $team->id, 'role' => Role::Member->value]);

    expect($membership->user->fresh()?->current_team_id)->toBe($membership->team->id);
});

it('detaches the user from a team', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    $membership = Membership::factory()->create();

    Livewire::test(TeamsRelationManager::class, ['ownerRecord' => $membership->user, 'pageClass' => ViewUser::class])
        ->loadTable()
        ->callAction(TestAction::make(DetachAction::class)->table($membership->team));

    $this->assertModelMissing($membership);
    $this->assertModelExists($membership->team);
});

it('links each team to its page', function (): void {
    asUser();
    $membership = Membership::factory()->create();

    $page = Livewire::test(TeamsRelationManager::class, ['ownerRecord' => $membership->user, 'pageClass' => ViewUser::class])->loadTable();

    $page->assertActionHasUrl(
        TestAction::make('view')->table($membership->team),
        TeamResource::getUrl('view', ['record' => $membership->team]),
    );
});
