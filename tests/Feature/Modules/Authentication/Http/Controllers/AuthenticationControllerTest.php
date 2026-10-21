<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Models\User;
use WorkOS\Client;
use WorkOS\RequestClient\RequestClientInterface;

beforeEach(function (): void {
    config([
        'services.workos.client_id' => 'client_test',
        'services.workos.secret' => 'sk_test',
        'services.workos.redirect_url' => 'https://app.test/authenticate',
    ]);
});

afterEach(function (): void {
    Client::setRequestClient(null);
});

/**
 * Answer the WorkOS code exchange with the given profile, so the
 * callback runs without reaching the WorkOS API.
 *
 * @param  array<string, mixed>  $profile
 */
function fakeWorkOSAuthentication(array $profile = []): void
{
    Client::setRequestClient(new class($profile) implements RequestClientInterface
    {
        /**
         * @param  array<string, mixed>  $profile
         */
        public function __construct(private array $profile) {}

        /**
         * @return array{0: string, 1: array<string, string>, 2: int}
         */
        public function request($method, $url, ?array $headers = null, ?array $params = null): array
        {
            return [(string) json_encode([
                'user' => [
                    'object' => 'user',
                    'id' => 'user_01',
                    'email' => 'ada@example.com',
                    'first_name' => 'Ada',
                    'last_name' => 'Lovelace',
                    'profile_picture_url' => null,
                    ...$this->profile,
                ],
                'organization_id' => null,
                'access_token' => 'access-token',
                'refresh_token' => 'refresh-token',
            ]), [], 200];
        }
    });
}

/**
 * Send the WorkOS callback with a state matching the session's.
 */
function sendWorkOSCallback(string $state = 'state-01'): \Illuminate\Testing\TestResponse
{
    /** @var \Tests\TestCase $test */
    $test = test();

    return $test
        ->withSession(['state' => json_encode(['state' => 'state-01'])])
        ->get(route('authenticate', [
            'code' => 'code-01',
            'state' => json_encode(['state' => $state]),
        ]));
}

describe('login', function (): void {
    it('redirects a guest to WorkOS AuthKit', function (): void {
        /** @var \Tests\TestCase $this */
        $response = $this->get(route('login'));

        expect($response->headers->get('Location'))->toStartWith('https://api.workos.com/user_management/authorize');
        $response->assertSessionHas('state');
    });

    it('redirects a signed-in user away', function (): void {
        /** @var \Tests\TestCase $this */
        asUser();

        $response = $this->get(route('login'));

        $response->assertRedirect('/');
    });
});

describe('authenticate', function (): void {
    it('signs in a new user and sends them home', function (): void {
        /** @var \Tests\TestCase $this */
        fakeWorkOSAuthentication();

        $response = sendWorkOSCallback();

        $user = User::query()->where('external_id', 'user_01')->sole();
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    });

    it('signs in an existing user without creating another', function (): void {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'ada@example.com', 'external_id' => null]);
        fakeWorkOSAuthentication();

        $response = sendWorkOSCallback();

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount(User::class, 1);
        expect($user->fresh()?->external_id)->toBe('user_01');
    });

    it('logs out a deactivated user', function (): void {
        User::factory()->inactive()->create(['external_id' => 'user_01']);
        fakeWorkOSAuthentication();

        $response = sendWorkOSCallback();

        $response->assertRedirectToRoute('logout');
    });

    it('rejects a callback whose state does not match the session', function (): void {
        /** @var \Tests\TestCase $this */
        fakeWorkOSAuthentication();

        $response = sendWorkOSCallback(state: 'forged');

        $response->assertForbidden();
        $this->assertGuest();
        $this->assertDatabaseEmpty(User::class);
    });
});

describe('logout', function (): void {
    it('signs the user out and sends them home', function (): void {
        /** @var \Tests\TestCase $this */
        asUser();

        $response = $this->get(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    });

    it('redirects a guest to the login page', function (): void {
        /** @var \Tests\TestCase $this */
        $response = $this->get(route('logout'));

        $response->assertRedirectToRoute('login');
    });
});
