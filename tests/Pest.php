<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Setup the test environment for
// Feature and Integration tests.
pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        /** @var \Tests\TestCase $this */
        $this->withoutVite();
    })
    ->in('Feature');

// Setup the test environment for
// Unit tests.
pest()->extend(TestCase::class)
    ->in('Unit');

/**
 * Act as a user when executing tests.
 */
function asUser(?User $user = null): User
{
    /** @var \App\Modules\Core\Identity\Models\User $user */
    test()->actingAs($user ??= User::factory()->create());

    return $user;
}
