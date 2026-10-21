<?php

namespace App\Modules\Core\Identity\Data;

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
}
