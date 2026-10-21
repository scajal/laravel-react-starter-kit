<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Http\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

/**
 * A team as the frontend receives it.
 */
class TeamData extends Data
{
    /**
     * Create a new team data instance.
     */
    public function __construct(
        public string $id,
        public bool $isActive,
        public string $name,
        public string $slug,
        public ?CarbonImmutable $createdAt,
        public ?CarbonImmutable $updatedAt,
    ) {}
}
