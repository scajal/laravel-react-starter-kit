<?php

declare(strict_types=1);

use App\Modules\Administration\Panels\Administration\Resources\Teams\Pages\CreateTeam;
use App\Modules\Core\Identity\Models\Team;
use Livewire\Livewire;

it('creates a team with a slug derived from its name', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();

    $page = Livewire::test(CreateTeam::class)
        ->fillForm(['name' => 'Acme Corporation'])
        ->call('create');

    $page->assertHasNoFormErrors()->assertNotified()->assertRedirect();
    $this->assertDatabaseHas(Team::class, ['name' => 'Acme Corporation', 'slug' => 'acme-corporation']);
});

it('creates a team with the slug given', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();

    $page = Livewire::test(CreateTeam::class)
        ->fillForm(['name' => 'Acme Corporation', 'slug' => 'acme'])
        ->call('create');

    $page->assertHasNoFormErrors();
    $this->assertDatabaseHas(Team::class, ['name' => 'Acme Corporation', 'slug' => 'acme']);
});

it('requires a name', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();

    $page = Livewire::test(CreateTeam::class)
        ->fillForm(['name' => null])
        ->call('create');

    $page->assertHasFormErrors(['name' => 'required'])->assertNotNotified();
    $this->assertDatabaseEmpty(Team::class);
});

it('rejects a slug another team uses', function (): void {
    /** @var \Tests\TestCase $this */
    asUser();
    Team::factory()->create(['slug' => 'acme']);

    $page = Livewire::test(CreateTeam::class)
        ->fillForm(['name' => 'Acme Corporation', 'slug' => 'acme'])
        ->call('create');

    $page->assertHasFormErrors(['slug' => 'unique']);
    $this->assertDatabaseCount(Team::class, 1);
});
