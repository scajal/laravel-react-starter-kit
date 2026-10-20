<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Enums\Team;

use Filament\Support\Contracts\HasLabel;

/**
 * The things a member may do on a team. Each role grants a
 * fixed set of them.
 *
 * @see \App\Modules\Core\Identity\Enums\Team\Role
 */
enum Permission: string implements HasLabel
{
    /**
     * Get the label for the permission.
     */
    public function getLabel(): string
    {
        return match ($this) {
            default => '',
        };
    }
}
