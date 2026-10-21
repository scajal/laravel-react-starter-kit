<?php

namespace App\Modules\Core\Identity\Data\Models;

use App\Modules\Core\Identity\Models\Team;

class TeamData extends \Spatie\LaravelData\Data
{
    /**
     * Create an instance of the class.
     */
    public function __construct(
        /**
         * The team's key.
         */
        public string $id,

        /**
         * Indicates if the team is currently active.
         */
        public bool $isActive,

        /**
         * The team's name.
         */
        public string $name,

        /**
         * The team's slug.
         */
        public string $slug,
    ) {}

    /**
     * Create a data's instance from the provided model.
     */
    public static function fromModel(Team $team): self
    {
        return new self(
            id: $team->getKey(), // @phpstan-ignore argument.type (Keys are always strings.)
            isActive: $team->is_active,
            name: $team->name,
            slug: $team->slug,
        );
    }
}
