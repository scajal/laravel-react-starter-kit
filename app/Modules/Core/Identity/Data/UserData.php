<?php

namespace App\Modules\Core\Identity\Data;

class UserData extends \Spatie\LaravelData\Data
{
    /**
     * Create an instance of the class.
     */
    public function __construct(
        /**
         * The user's key.
         */
        public string $id,

        /**
         * The user's avatar URL.
         */
        public ?string $avatar,

        /**
         * The user's email address.
         */
        public string $email,

        /**
         * Indicates if the user is currently active.
         */
        public bool $isActive,

        /**
         * The user's name.
         */
        public string $name,
    ) {}
}
