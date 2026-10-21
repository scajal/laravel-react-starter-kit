import type { Permission } from "@/types/modules/core/identity/enums/team/permission";
import type { Appearance, Locale } from "@/types/modules/core/identity/enums/user/preference-key";
import type { Team } from "@/types/modules/core/identity/models/team";
import type { User } from "@/types/modules/core/identity/models/user";

/**
 * Props merged by `App\Modules\Support\Http\Middleware\HandleInertiaRequests`
 * into every page. The `errors` prop shared by Inertia itself is typed by
 * `@inertiajs/core`.
 */
export type SharedInertiaProps = {
    /** The colour scheme the user chose; "system" follows the device. */
    appearance: Appearance;
    /** What the user may do. */
    authorization: {
        /** Whether the user may open the administration panel; null for guests. */
        canAccessAdministration: boolean | null;
        /** The permissions the user's role grants on their current team. */
        teamPermissions: Array<Permission>;
    };
    /** The locale the app is shown in. */
    locale: Locale;
    /**
     * The signed-in user, or null for guests. The current team is always
     * loaded, since resolving `authorization` reads it.
     */
    user: (User & { current_team: Team | null }) | null;
};
