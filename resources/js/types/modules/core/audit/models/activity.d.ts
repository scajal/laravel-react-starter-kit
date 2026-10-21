import type { Membership } from "@/types/modules/core/identity/models/membership";
import type { Team } from "@/types/modules/core/identity/models/team";
import type { User } from "@/types/modules/core/identity/models/user";
import type { Preference } from "@/types/modules/core/identity/models/user/preference";

/**
 * The values of `Spatie\Activitylog\Enums\ActivityEvent`: the model events
 * that are logged.
 */
export type ActivityEvent = "created" | "deleted" | "restored" | "updated";

/**
 * The class of each model that logs its activity, as stored in
 * `subject_type`, mapped to its serialized shape.
 */
export type ActivitySubjects = {
    "App\\Modules\\Core\\Identity\\Models\\Membership": Membership;
    "App\\Modules\\Core\\Identity\\Models\\Team": Team;
    "App\\Modules\\Core\\Identity\\Models\\User": User;
    "App\\Modules\\Core\\Identity\\Models\\User\\Preference": Preference;
};

/**
 * The class of each model that causes activity, as stored in
 * `causer_type`, mapped to its serialized shape.
 */
export type ActivityCausers = {
    "App\\Modules\\Core\\Identity\\Models\\User": User;
};

/**
 * The attributes of the subject an activity recorded: the new ones under
 * `attributes` and, for updates and deletions, the previous ones under `old`.
 */
export type ActivityAttributeChanges = {
    attributes?: Record<string, unknown>;
    old?: Record<string, unknown>;
};

/**
 * The attributes of every activity, whatever its subject and causer.
 */
type ActivityAttributes = {
    /** The ULID of the activity. */
    id: string;
    attribute_changes: ActivityAttributeChanges | null;
    description: string;
    event: ActivityEvent | null;
    log_name: string | null;
    /** The custom properties; an empty collection is sent as an empty array. */
    properties: Record<string, unknown> | [] | null;
    /** When the activity was created, as an ISO 8601 string. */
    created_at: string | null;
    /** When the activity was last updated, as an ISO 8601 string. */
    updated_at: string | null;
};

/**
 * The subject of an activity, discriminated by `subject_type`. The `subject`
 * relation is only present when it was loaded.
 */
type ActivitySubject =
    | {
          [Type in keyof ActivitySubjects]: {
              subject_type: Type;
              /** The ULID of the subject. */
              subject_id: string;
              subject?: ActivitySubjects[Type] | null;
          };
      }[keyof ActivitySubjects]
    | { subject_type: null; subject_id: null; subject?: null };

/**
 * The causer of an activity, discriminated by `causer_type`. The `causer`
 * relation is only present when it was loaded.
 */
type ActivityCauser =
    | {
          [Type in keyof ActivityCausers]: {
              causer_type: Type;
              /** The ULID of the causer. */
              causer_id: string;
              causer?: ActivityCausers[Type] | null;
          };
      }[keyof ActivityCausers]
    | { causer_type: null; causer_id: null; causer?: null };

/**
 * A serialized `App\Modules\Core\Audit\Models\Activity`: an entry of the
 * activity log.
 */
export type Activity = ActivityAttributes & ActivitySubject & ActivityCauser;
