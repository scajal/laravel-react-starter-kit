<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Enums\Team;

use App\Modules\Support\Concerns\AsOptions;
use Filament\Support\Contracts\HasLabel;

/**
 * The things a member may do on a team. Each role grants a
 * fixed set of them.
 *
 * @see \App\Modules\Core\Identity\Enums\Team\Role
 */
enum Permission: string implements HasLabel
{
    use AsOptions;

    /**
     * Represents the wildcard permission which allows
     * all the actions on the team.
     */
    case Wildcard = '*';

    /**
     * Get the label for the permission.
     *
     * @codeCoverageIgnore Unreachable until the Permission enum declares its first case.
     */
    /**
     * Get the label for the role.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Wildcard => trans('Wildcard'),
        };
    }
}
