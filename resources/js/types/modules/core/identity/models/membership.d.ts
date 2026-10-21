import type { Role } from "@/types/modules/core/identity/enums/team/role";
import type { Team } from "@/types/modules/core/identity/models/team";
import type { User } from "@/types/modules/core/identity/models/user";

/**
 * A serialized `App\Modules\Core\Identity\Models\Membership`: a user's
 * place in a team and their role there.
 *
 * Relations are only present when they were loaded.
 */
export type Membership = {
    /** The ULID of the membership. */
    id: string;
    role: Role;
    /** The ULID of the team. */
    team_id: string;
    /** The ULID of the user. */
    user_id: string;
    /** When the membership was created, as an ISO 8601 string. */
    created_at: string | null;
    /** When the membership was last updated, as an ISO 8601 string. */
    updated_at: string | null;

    /** The team that the membership belongs to. */
    team?: Team;
    /** The user that belongs to this membership. */
    user?: User;
};

/**
 * The membership as it is nested under `membership` when reached through
 * the `User::teams()` or `Team::users()` relations: the pivot columns only,
 * without the membership's own key.
 */
export type MembershipPivot = Pick<Membership, "role" | "team_id" | "user_id" | "created_at" | "updated_at">;
