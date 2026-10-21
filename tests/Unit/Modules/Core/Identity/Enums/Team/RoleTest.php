<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\Team\Permission;
use App\Modules\Core\Identity\Enums\Team\Role;
use Illuminate\Support\Facades\App;

test('each role has a translated label', function (Role $role, string $english, string $spanish): void {
    expect($role->getLabel())->toBe($english);

    App::setLocale('es');

    expect($role->getLabel())->toBe($spanish);
})->with([
    'administrator' => [Role::Administrator, 'Administrator', 'Administrador'],
    'member' => [Role::Member, 'Member', 'Miembro'],
    'owner' => [Role::Owner, 'Owner', 'Propietario'],
]);

test('the owner is granted every permission', function (): void {
    expect(Role::Owner->permissions())->toBe(Permission::cases());
});

test('administrators and members are granted no permissions yet', function (Role $role): void {
    expect($role->permissions())->toBe([]);
})->with([Role::Administrator, Role::Member]);

test('only the owner is granted the wildcard permission', function (Role $role, bool $isGranted): void {
    expect($role->grants(Permission::Wildcard))->toBe($isGranted);
})->with([
    'owner' => [Role::Owner, true],
    'administrator' => [Role::Administrator, false],
    'member' => [Role::Member, false],
]);
