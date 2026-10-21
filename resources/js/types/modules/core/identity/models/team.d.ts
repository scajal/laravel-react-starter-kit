import type { Membership, MembershipPivot } from "@/types/modules/core/identity/models/membership";
import type { User } from "@/types/modules/core/identity/models/user";
import type { HasActivation } from "@/types/modules/support/concerns/has-activation";

/**
 * A serialized `App\Modules\Core\Identity\Models\Team`.
 *
 * Relations are only present when they were loaded.
 */
export type Team = HasActivation & {
    /** The ULID of the team. */
    id: string;
    name: string;
    /** The unique, lowercase slug derived from the name. */
    slug: string;
    /** When the team was created, as an ISO 8601 string. */
    created_at: string | null;
    /** When the team was last updated, as an ISO 8601 string. */
    updated_at: string | null;

    /** The memberships of the team. */
    memberships?: Array<Membership>;
    /** The users that belong to the team, each with the membership joining them. */
    users?: Array<User & { membership: MembershipPivot }>;
};
