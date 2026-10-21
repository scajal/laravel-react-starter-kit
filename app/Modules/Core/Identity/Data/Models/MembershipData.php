<?php

namespace App\Modules\Core\Identity\Data\Models;

use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use Spatie\LaravelData\Lazy;

class MembershipData extends \Spatie\LaravelData\Data
{
    /**
     * Create an instance of the class.
     */
    public function __construct(
        /**
         * The membership's key.
         */
        public string $id,

        /**
         * The associated role.
         */
        public Role $role,

        /**
         * The team the membership belongs to.
         */
        public Lazy|TeamData $team,

        /**
         * The user the membership belongs to.
         */
        public Lazy|UserData $user,
    ) {}

    /**
     * Create a data's instance from the provided model.
     */
    public static function fromModel(Membership $membership): self
    {
        return new self(
            id: $membership->getKey(), // @phpstan-ignore argument.type (Keys are always strings.)
            role: $membership->role,
            team: Lazy::whenLoaded('team', $membership, fn (): TeamData => TeamData::from($membership->team)),
            user: Lazy::whenLoaded('user', $membership, fn (): UserData => UserData::from($membership->user)),
        );
    }
}
