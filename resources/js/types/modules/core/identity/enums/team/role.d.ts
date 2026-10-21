/**
 * The values of `App\Modules\Core\Identity\Enums\Team\Role`: the role a
 * membership carries.
 *
 * - `admin`: helps manage the team, but neither deletes it nor pays for it.
 * - `member`: only invites people and leaves.
 * - `owner`: created the team; there is exactly one per team.
 *
 * @see {@link import("./permission").Permission}
 */
export type Role = "admin" | "member" | "owner";
