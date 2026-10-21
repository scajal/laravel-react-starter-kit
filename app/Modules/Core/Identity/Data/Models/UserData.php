<?php

namespace App\Modules\Core\Identity\Data\Models;

use App\Modules\Core\Identity\Models\User;

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

    /**
     * Create a data's instance from the provided model.
     */
    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->getKey(), // @phpstan-ignore-line argument.type Keys are always strings.
            avatar: $user->avatar,
            email: $user->email,
            isActive: $user->is_active,
            name: $user->name,
        );
    }
}
