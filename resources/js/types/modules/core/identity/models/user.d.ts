import type { Membership, MembershipPivot } from "@/types/modules/core/identity/models/membership";
import type { Team } from "@/types/modules/core/identity/models/team";
import type { Preference } from "@/types/modules/core/identity/models/user/preference";
import type { HasActivation } from "@/types/modules/support/concerns/has-activation";

/**
 * A serialized `App\Modules\Core\Identity\Models\User`. The `external_id`
 * and `remember_token` attributes are hidden and never sent.
 *
 * Relations are only present when they were loaded.
 */
export type User = HasActivation & {
    /** The ULID of the user. */
    id: string;
    /** The URL of the user's avatar, from WorkOS. */
    avatar: string | null;
    /** The ULID of the team the user is working in. */
    current_team_id: string | null;
    email: string;
    /** When the email was verified, as an ISO 8601 string. */
    email_verified_at: string | null;
    name: string;
    /** When the user was created, as an ISO 8601 string. */
    created_at: string | null;
    /** When the user was last updated, as an ISO 8601 string. */
    updated_at: string | null;

    /** The team the user is working in. */
    current_team?: Team | null;
    /** The user's memberships, one per team they belong to. */
    memberships?: Array<Membership>;
    /** The preferences the user stored. */
    preferences?: Array<Preference>;
    /** The teams the user belongs to, each with the membership joining them. */
    teams?: Array<Team & { membership: MembershipPivot }>;
};
