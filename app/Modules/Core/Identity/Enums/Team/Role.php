<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Enums\Team;

use App\Modules\Support\Concerns\AsOptions;
use Filament\Support\Contracts\HasLabel;

/**
 * The role a membership carries. Every team has exactly one
 * Owner, the user who created it, and the role decides what
 * the member may do.
 *
 * @see \App\Modules\Core\Identity\Enums\Team\Permission
 */
enum Role: string implements HasLabel
{
    use AsOptions;

    /**
     * Represents a member who helps manage the team, but
     * neither deletes it nor pays for it.
     */
    case Administrator = 'admin';

    /**
     * Represents a member who only invites people and
     * leaves. Assigning it is a pro feature.
     */
    case Member = 'member';

    /**
     * Represents the user who created the team. There is
     * exactly one per team and it is never assignable.
     */
    case Owner = 'owner';

    /**
     * Get the label for the role.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Administrator => trans('Administrator'),
            self::Member => trans('Member'),
            self::Owner => trans('Owner'),
        };
    }

    /**
     * Check whether the role grants the given
     * permission.
     *
     * @codeCoverageIgnore Unreachable until the Permission enum declares its first case.
     */
    public function grants(Permission $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * Get the permissions the role grants.
     *
     * @return array<int, \App\Modules\Core\Identity\Enums\Team\Permission>
     */
    public function permissions(): array
    {
        return match ($this) {
            // The owner may do everything.
            self::Owner => Permission::cases(),
            default => [],
        };
    }
}
