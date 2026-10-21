<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Users\Pages\ListUsers;
use App\Modules\Core\Identity\Models\User;
use Livewire\Livewire;

it('lists the users', function (): void {
    $admin = asUser();
    $user = User::factory()->create();

    $page = Livewire::test(ListUsers::class)->loadTable();

    $page->assertOk()->assertCanSeeTableRecords([$admin, $user]);
});

it('searches the users by email and by name', function (string $column, string $search): void {
    asUser(User::factory()->create(['email' => 'admin@example.com', 'name' => 'Grace Hopper']));
    $ada = User::factory()->create(['email' => 'ada@example.com', 'name' => 'Ada Lovelace']);

    $page = Livewire::test(ListUsers::class)
        ->loadTable()
        ->searchTableColumns([$column => $search]);

    $page->assertCanSeeTableRecords([$ada])->assertCountTableRecords(1);
})->with([
    'email' => ['email', 'ada@'],
    'name' => ['name', 'lovelace'],
]);

it('filters the users by their active state', function (): void {
    $admin = asUser();
    $inactive = User::factory()->inactive()->create();

    $page = Livewire::test(ListUsers::class)
        ->loadTable()
        ->filterTable('is_active', false);

    $page->assertCanSeeTableRecords([$inactive])->assertCanNotSeeTableRecords([$admin]);
});

it('labels each user as active or inactive', function (): void {
    $admin = asUser();
    $inactive = User::factory()->inactive()->create();

    $page = Livewire::test(ListUsers::class)->loadTable();

    $page->assertTableColumnFormattedStateSet('is_active', 'Active', $admin)
        ->assertTableColumnFormattedStateSet('is_active', 'Inactive', $inactive);
});

it('links to the WorkOS dashboard in a new tab', function (): void {
    asUser();

    $page = Livewire::test(ListUsers::class);

    $page->assertActionHasUrl('workOs', 'https://dashboard.workos.com/')
        ->assertActionShouldOpenUrlInNewTab('workOs');
});
