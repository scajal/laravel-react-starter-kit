<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Http\Data\AuthorizationData;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;

it('lets a guest do nothing', function (): void {
    $authorization = AuthorizationData::forUser(null);

    expect($authorization->canAccessAdministration)->toBeFalse()
        ->and($authorization->teamPermissions)->toBe([]);
});

it('grants the permissions of the role on the current team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    Membership::factory()->for($team)->for($user)->create(['role' => Role::Owner]);
    $user->switchTeam($team);

    expect(AuthorizationData::forUser($user)->teamPermissions)->toBe(Role::Owner->permissions());
});
