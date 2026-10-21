<?php

namespace App\Modules\Support\Http\Middleware;

use App\Modules\Support\Http\Attributes\AlwaysShared;
use App\Modules\Support\Http\Data\SharedData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Spatie\LaravelData\Support\DataConfig;

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
        /** @var array<string, mixed>|null $shared */
        $shared = null;

        $resolve = function () use ($request, &$shared): array {
            return $shared ??= SharedData::fromRequest($request)->toArray();
        };

        $props = [];

        foreach (app(DataConfig::class)->getDataClass(SharedData::class)->properties as $property) {
            $key = $property->outputMappedName ?? $property->name;
            $prop = fn (): mixed => $resolve()[$key];

            $props[$key] = $property->attributes->has(AlwaysShared::class) ? Inertia::always($prop) : $prop;
        }

        return [
            ...parent::share($request),
            ...$props,
        ];
    }
}
