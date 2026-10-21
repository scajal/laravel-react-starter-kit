import { Permission } from '../../Enums/Team';
export type AuthorizationData = {
can_access_administration: boolean,
team_permissions: Permission[],
};
export type TeamData = {
id: string,
is_active: boolean,
name: string,
slug: string,
created_at: string | null,
updated_at: string | null,
};
export type UserData = {
id: string,
avatar: string | null,
current_team_id: string | null,
email: string,
email_verified_at: string | null,
is_active: boolean,
name: string,
created_at: string | null,
updated_at: string | null,
};
