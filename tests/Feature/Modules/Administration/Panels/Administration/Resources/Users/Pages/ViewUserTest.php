<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Users\Pages\ViewUser;
use App\Modules\Administration\Panels\Administration\Resources\Users\RelationManagers\TeamsRelationManager;
use App\Modules\Core\Identity\Models\User;
use Livewire\Livewire;

it('shows the user with their teams', function (): void {
    asUser();
    $user = User::factory()->create(['email' => 'ada@example.com', 'name' => 'Ada Lovelace']);

    $page = Livewire::test(ViewUser::class, ['record' => $user->getRouteKey()]);

    $page->assertOk()
        ->assertSchemaStateSet(['email' => 'ada@example.com', 'name' => 'Ada Lovelace'])
        ->assertSeeLivewire(TeamsRelationManager::class);
});

it('deactivates an active user', function (): void {
    asUser();
    $user = User::factory()->create();

    $page = Livewire::test(ViewUser::class, ['record' => $user->getRouteKey()])
        ->assertActionHidden('activate')
        ->callAction('deactivate');

    $page->assertNotified('Saved');
    expect($user->fresh()?->is_active)->toBeFalse();
});

it('activates an inactive user', function (): void {
    asUser();
    $user = User::factory()->inactive()->create();

    $page = Livewire::test(ViewUser::class, ['record' => $user->getRouteKey()])
        ->assertActionHidden('deactivate')
        ->callAction('activate');

    $page->assertNotified('Saved');
    expect($user->fresh()?->is_active)->toBeTrue();
});
