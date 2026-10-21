<?php

declare(strict_types=1);

use App\Modules\Authentication\Support\AuthKitAuthentication;
use App\Modules\Core\Identity\Models\User;
use Laravel\WorkOS\User as WorkOSUser;

/**
 * Build the profile WorkOS signs a user in as.
 */
function workOSUser(string $id = 'user_01', string $email = 'ada@example.com', ?string $avatar = 'https://example.com/ada.png'): WorkOSUser
{
    return new WorkOSUser(
        id: $id,
        organizationId: null,
        firstName: 'Ada',
        lastName: 'Lovelace',
        email: $email,
        avatar: $avatar,
    );
}

it('creates an active, verified user from the WorkOS profile', function (): void {
    /** @var \Tests\TestCase $this */
    $this->travelTo('2026-01-15 09:30:00');

    $user = new AuthKitAuthentication()->createUsing(workOSUser());

    expect($user->fresh())
        ->avatar->toBe('https://example.com/ada.png')
        ->email->toBe('ada@example.com')
        ->email_verified_at->toDateTimeString()->toBe('2026-01-15 09:30:00')
        ->external_id->toBe('user_01')
        ->is_active->toBeTrue()
        ->name->toBe('Ada Lovelace');
});

it('creates a user with an empty avatar when WorkOS has none', function (): void {
    $user = new AuthKitAuthentication()->createUsing(workOSUser(avatar: null));

    expect($user->fresh()?->avatar)->toBe('');
});

it('finds a user by their WorkOS identifier', function (): void {
    $user = User::factory()->create(['external_id' => 'user_01']);

    $found = new AuthKitAuthentication()->findUsing(workOSUser(email: 'other@example.com'));

    expect($found?->is($user))->toBeTrue();
});

it('falls back to the email for a user whose identifier was never recorded', function (): void {
    $user = User::factory()->create(['email' => 'ada@example.com', 'external_id' => null]);

    $found = new AuthKitAuthentication()->findUsing(workOSUser());

    expect($found?->is($user))->toBeTrue();
});

it('prefers the user with the WorkOS identifier over the one with the email', function (): void {
    User::factory()->create(['email' => 'ada@example.com']);
    $user = User::factory()->create(['external_id' => 'user_01']);

    $found = new AuthKitAuthentication()->findUsing(workOSUser());

    expect($found?->is($user))->toBeTrue();
});

it('finds no user when neither the identifier nor the email match', function (): void {
    User::factory()->create();

    $found = new AuthKitAuthentication()->findUsing(workOSUser());

    expect($found)->toBeNull();
});

it('follows the WorkOS identifier and avatar on update', function (): void {
    $user = User::factory()->create(['avatar' => 'https://example.com/old.png', 'external_id' => 'user_old']);

    new AuthKitAuthentication()->updateUsing($user, workOSUser(id: 'user_new'));

    expect($user->fresh())
        ->avatar->toBe('https://example.com/ada.png')
        ->external_id->toBe('user_new');
});

it('keeps the stored avatar when WorkOS has none', function (): void {
    $user = User::factory()->create(['avatar' => 'https://example.com/old.png']);

    new AuthKitAuthentication()->updateUsing($user, workOSUser(avatar: null));

    expect($user->fresh()?->avatar)->toBe('https://example.com/old.png');
});
