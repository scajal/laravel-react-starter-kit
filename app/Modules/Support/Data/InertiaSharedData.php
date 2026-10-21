<?php

namespace App\Modules\Support\Data;

use App\Modules\Core\Identity\Data\TeamData;
use App\Modules\Core\Identity\Data\UserData;
use App\Modules\Core\Identity\Enums\User\Appearance;
use App\Modules\Core\Identity\Models\Team;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/**
 * This class is not instantiated in the backend in any way. It just
 * exists to generate the corresponding typescript type for the
 * shared properties and is used as contract in the Inertia middleware.
 */
abstract class InertiaSharedData extends \Spatie\LaravelData\Data
{
    /**
     * Create a new class instance.
     *
     * @param  array{canAccessAdministration: bool, teamPermissions: array<int, \App\Modules\Core\Identity\Enums\Team\Permission>}  $authorization
     * @param  'en' | 'es'  $locale
     */
    public function __construct(
        /**
         * The chosen colour scheme, from an unencrypted cookie the
         * client writes, so the server renders the right one on
         * the first paint instead of flashing. "system" leaves the
         * choice to the device.
         */
        public Appearance $appearance,

        /**
         * Indicate the permissions that the user has,
         * including the ones in the current team.
         */
        public array $authorization,

        /**
         * The team the user is working in.
         */
        public ?TeamData $currentTeam,

        /**
         * The resolved locale, read at render time so it
         * works for SSR requests too.
         */
        #[LiteralTypeScriptType(("'en' | 'es'"))]
        public string $locale,

        /**
         * The authenticated user.
         */
        public ?UserData $user,
    ) {}
}
