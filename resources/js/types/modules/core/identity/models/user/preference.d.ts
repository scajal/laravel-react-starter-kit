import type { PreferenceKey, PreferenceValues } from "@/types/modules/core/identity/enums/user/preference-key";
import type { User } from "@/types/modules/core/identity/models/user";

/**
 * A serialized `App\Modules\Core\Identity\Models\User\Preference`: a choice
 * a user made about how the app looks, reads or behaves.
 *
 * Narrowing on `key` narrows `value` to the values that preference accepts.
 */
export type Preference = {
    [Key in PreferenceKey]: {
        /** The ULID of the preference. */
        id: string;
        key: Key;
        /** The ULID of the user the preference belongs to. */
        user_id: string;
        value: PreferenceValues[Key];
        /** When the preference was created, as an ISO 8601 string. */
        created_at: string | null;
        /** When the preference was last updated, as an ISO 8601 string. */
        updated_at: string | null;

        /** The user the preference belongs to; only present when loaded. */
        user?: User;
    };
}[PreferenceKey];
