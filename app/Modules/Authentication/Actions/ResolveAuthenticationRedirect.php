<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Actions;

use App\Modules\Core\Identity\Models\User;
use Illuminate\Http\Request;

class ResolveAuthenticationRedirect
{
    /**
     * From the given request, resolve the redirect URL
     * to which the user should be sent after authenticating.
     */
    public function fromRequest(Request $request): string
    {
        // If the user was sent to login from a page that requires
        // authentication (such as an invitation link), go back there.
        if ($intended = $this->intended($request)) {
            return $intended;
        }

        /** @var \App\Modules\Core\Identity\Models\User $user */
        $user = $request->user();

        return $this->forUser($user);
    }

    /**
     * Resolve the redirect URL for the given user, without
     * touching the request's session.
     */
    public function forUser(User $user): string
    {
        // A user who belongs to no team has nowhere to go:
        // explain it instead of failing.
        if ($user->teams->isEmpty()) {
            // @todo: Redirect to team creation page.
            return '/';
        }

        // Otherwise, land on the application's home.
        // @todo: Redirect to home page.
        return '/';
    }

    /**
     * Pull the intended URL stored by the guest redirect
     * from the session, if any.
     */
    protected function intended(Request $request): ?string
    {
        // If the request doesn't have a session, return null.
        if (! $request->hasSession()) {
            return null;
        }

        // Pull the intended URL from the session.
        $intended = $request->session()->pull('url.intended');

        // If the intended URL is not a string or is an empty string, return null.
        return is_string($intended) && $intended !== '' ? $intended : null;
    }
}
