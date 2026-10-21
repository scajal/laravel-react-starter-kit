<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Http\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

/**
 * A user as the frontend receives it. The WorkOS identifier and the
 * remember token never leave the server.
 */
class UserData extends Data
{
    /**
     * Create a new user data instance.
     */
    public function __construct(
        public string $id,
        public ?string $avatar,
        public ?string $currentTeamId,
        public string $email,
        public ?CarbonImmutable $emailVerifiedAt,
        public bool $isActive,
        public string $name,
        public ?CarbonImmutable $createdAt,
        public ?CarbonImmutable $updatedAt,
    ) {}
}
