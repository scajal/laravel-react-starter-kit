<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Http\Middleware;

use App\Modules\Core\Identity\Enums\User\Locale;
use App\Modules\Core\Identity\Enums\User\PreferenceKey;
use App\Modules\Core\Identity\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Resolve the default locale from the
        // incoming request.
        $locale = $this->resolve($request);

        // If the locale is installed, set it as
        // the app's locale.
        if (in_array($locale, $this->locales(), true)) {
            App::setLocale($locale);

            // Set the locale cookie, which also rewrites a stale
            // one on a device the member did not choose it on.
            Cookie::queue(PreferenceKey::Locale->cookie($locale));

            // If there's a session instance, set the
            // locale as the session's preferred locale.
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }

        return $next($request);
    }

    /**
     * Resolve the locale for the given request: route segment,
     * then the signed-in member's stored locale, then the locale
     * cookie, then the browser's preferred language, then the
     * application's default locale.
     */
    public function resolve(Request $request): string
    {
        // First, check if the locale is coming from
        // the route segment.
        if (PreferenceKey::Locale->accepts($locale = $request->route('locale'))) {
            return $locale;
        }

        // Then, check if the signed-in member stored one.
        if (PreferenceKey::Locale->accepts($stored = $this->stored($request))) {
            return $stored;
        }

        // Then, check if the locale is coming from
        // the cookie.
        if (PreferenceKey::Locale->accepts($cookie = $request->cookie(PreferenceKey::Locale->cookieName()))) {
            return $cookie;
        }

        // Then, the browser's preferred language, when it sent one.
        if ($request->headers->has('Accept-Language') && PreferenceKey::Locale->accepts($preferred = $request->getPreferredLanguage($this->locales()))) {
            return $preferred;
        }

        // Finally, the application's default locale.
        return config()->string('app.locale');
    }

    /**
     * Get the locale the signed-in member stored. A member who
     * never stored one adopts the locale cookie, so the choice
     * they made before signing in follows them from now on.
     */
    protected function stored(Request $request): ?string
    {
        /** @var \App\Modules\Core\Identity\Models\User|null $user */
        $user = $request->user();

        // A visitor who is not signed in has nothing stored.
        if (! $user instanceof User) {
            return null;
        }

        // A stored locale wins over whatever the cookie says.
        if (($stored = $user->preference(PreferenceKey::Locale)) !== null) {
            return $stored;
        }

        // Otherwise, a valid cookie becomes the stored locale.
        if (! PreferenceKey::Locale->accepts($cookie = $request->cookie(PreferenceKey::Locale->cookieName()))) {
            return null;
        }

        return $user->rememberPreference(PreferenceKey::Locale, $cookie)->value;
    }

    /**
     * Get the list of installed locale codes.
     *
     * @return array<int, string>
     */
    protected function locales(): array
    {
        /** @var array<int, string> */
        $locales = array_column(Locale::cases(), 'value');

        return $locales;
    }
}
