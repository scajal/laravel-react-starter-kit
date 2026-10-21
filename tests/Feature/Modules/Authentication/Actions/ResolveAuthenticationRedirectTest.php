<?php

declare(strict_types=1);

use App\Modules\Authentication\Actions\ResolveAuthenticationRedirect;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\User;
use Illuminate\Http\Request;

/**
 * Build a request signed in as the given user, carrying a session
 * with the given intended URL.
 */
function authenticatedRequest(User $user, mixed $intended = null): Request
{
    $request = Request::create('/authenticate');
    $request->setLaravelSession($session = app('session')->driver());
    $request->setUserResolver(fn (): User => $user);

    if ($intended !== null) {
        $session->put('url.intended', $intended);
    }

    return $request;
}

it('returns to the page that required authentication and forgets it', function (): void {
    $request = authenticatedRequest(User::factory()->create(), '/invitations/01');

    $redirect = new ResolveAuthenticationRedirect()->fromRequest($request);

    expect($redirect)->toBe('/invitations/01')
        ->and($request->session()->has('url.intended'))->toBeFalse();
});

it('ignores an intended URL that is not a usable string', function (mixed $intended): void {
    $request = authenticatedRequest(User::factory()->create(), $intended);

    $redirect = new ResolveAuthenticationRedirect()->fromRequest($request);

    expect($redirect)->toBe('/');
})->with([
    'an empty string' => '',
    'an array' => [['/invitations/01']],
]);

it('resolves the redirect for the user on a request without a session', function (): void {
    $request = Request::create('/authenticate');
    $request->setUserResolver(fn (): User => User::factory()->create());

    $redirect = new ResolveAuthenticationRedirect()->fromRequest($request);

    expect($redirect)->toBe('/');
});

it('lands the user on the home page', function (Closure $user): void {
    $redirect = new ResolveAuthenticationRedirect()->forUser($user());

    expect($redirect)->toBe('/');
})->with([
    'without a team' => fn (): User => User::factory()->create(),
    'with a team' => fn (): User => Membership::factory()->create()->user,
]);
