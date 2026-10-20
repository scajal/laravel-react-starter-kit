<?php

namespace App\Modules\Support\Http\Middleware;

use App\Modules\Core\Identity\Http\Middleware\HandleAppearance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            // The chosen colour scheme, from an unencrypted cookie the
            // client writes, so the server renders the right one on the
            // first paint instead of flashing. "system" leaves the
            // choice to the device.
            'appearance' => fn (): string => HandleAppearance::resolve($request),

            // The resolved locale, read at render time so it
            // works for SSR requests too.
            'locale' => fn (): string => App::currentLocale(),
        ];
    }
}
