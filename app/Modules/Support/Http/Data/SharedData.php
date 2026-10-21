<?php

declare(strict_types=1);

namespace App\Modules\Support\Http\Data;

use App\Modules\Core\Identity\Enums\User\Appearance;
use App\Modules\Core\Identity\Http\Data\AuthorizationData;
use App\Modules\Core\Identity\Http\Data\TeamData;
use App\Modules\Core\Identity\Http\Data\UserData;
use App\Modules\Core\Identity\Http\Middleware\HandleAppearance;
use App\Modules\Support\Http\Attributes\AlwaysShared;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Spatie\LaravelData\Data;

/**
 * The props shared with every page. Each property is shared as its
 * own prop, so this class is the single place to add one.
 *
 * @see \App\Modules\Support\Http\Middleware\HandleInertiaRequests
 */
class SharedData extends Data
{
    /**
     * Create a new shared data instance.
     */
    public function __construct(
        public Appearance $appearance,
        public AuthorizationData $authorization,
        public ?TeamData $currentTeam,
        public string $locale,
        #[AlwaysShared]
        public ?UserData $user,
    ) {}

    /**
     * Resolve the shared props for the given request.
     */
    public static function fromRequest(Request $request): self
    {
        /** @var \App\Modules\Core\Identity\Models\User|null $user */
        $user = $request->user();

        return new self(
            // The chosen colour scheme, from an unencrypted cookie the
            // client writes, so the server renders the right one on the
            // first paint instead of flashing. "system" leaves the
            // choice to the device.
            appearance: Appearance::from(HandleAppearance::resolve($request)),

            // Indicate the permissions that the user has,
            // including the ones in the current team.
            authorization: AuthorizationData::forUser($user),

            // The team the user is working in.
            currentTeam: TeamData::optional($user?->currentTeam),

            // The resolved locale.
            locale: App::currentLocale(),

            // The authenticated user.
            user: UserData::optional($user),
        );
    }
}
