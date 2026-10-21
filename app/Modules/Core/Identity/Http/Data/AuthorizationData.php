<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Http\Data;

use App\Modules\Core\Identity\Models\User;
use Filament\Facades\Filament;
use Spatie\LaravelData\Data;

/**
 * What the signed-in user may do, so the frontend can show or hide
 * what they cannot reach. The server still authorizes every request.
 */
class AuthorizationData extends Data
{
    /**
     * Create a new authorization data instance.
     *
     * @param  array<int, \App\Modules\Core\Identity\Enums\Team\Permission>  $teamPermissions
     */
    public function __construct(
        public bool $canAccessAdministration,
        public array $teamPermissions,
    ) {}

    /**
     * Resolve what the given user may do. A guest may do nothing.
     */
    public static function forUser(?User $user): self
    {
        if ($user === null) {
            return new self(canAccessAdministration: false, teamPermissions: []);
        }

        $currentTeam = $user->currentTeam;

        return new self(
            canAccessAdministration: $user->canAccessPanel(Filament::getPanel('administration')),
            teamPermissions: $currentTeam !== null ? $user->teamRole($currentTeam)?->permissions() ?? [] : [],
        );
    }
}
