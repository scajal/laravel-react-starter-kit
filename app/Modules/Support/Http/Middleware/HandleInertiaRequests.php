<?php

namespace App\Modules\Support\Http\Middleware;

use App\Modules\Core\Identity\Data\Models\TeamData;
use App\Modules\Core\Identity\Data\Models\UserData;
use App\Modules\Core\Identity\Enums\User\Appearance;
use App\Modules\Core\Identity\Http\Middleware\HandleAppearance;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default: one per property of
     * the shared data, which is resolved once, at render time, so it
     * reflects whatever the request changed and works for SSR too.
     *
     * @see https://inertiajs.com/shared-data
     * @see \App\Modules\Support\Http\Data\SharedData
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var \App\Modules\Core\Identity\Models\User|null $user */
        $user = $request->user();

        return [
            ...parent::share($request),

            // The chosen colour scheme, from an unencrypted cookie the
            // client writes, so the server renders the right one on the
            // first paint instead of flashing. "system" leaves the
            // choice to the device.
            'appearance' => fn (): Appearance => HandleAppearance::resolve($request),

            // Indicate the permissions that the user has,
            // including the ones in the current team.
            'authorization' => fn (): array => [
                'canAccessAdministration' => (bool) $user?->canAccessPanel(Filament::getPanel('administration')),
                'teamPermissions' => $user?->currentTeam ? $user->teamRole($user->currentTeam)?->permissions() ?? [] : [],
            ],

            // The team the user is working in.
            'current_team' => fn (): ?TeamData => TeamData::optional($user?->currentTeam),

            // The resolved locale, read at render time so it
            // works for SSR requests too.
            'locale' => fn (): string => App::currentLocale(),

            // The authenticated user.
            'user' => Inertia::always(fn (): ?UserData => UserData::optional($user)),
        ];
    }
}
