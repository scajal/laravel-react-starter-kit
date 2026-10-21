import { Role } from '../../Enums/Team';
export type MembershipData = {
id: string,
role: Role,
team?: TeamData,
user?: UserData,
};
export type TeamData = {
id: string,
is_active: boolean,
name: string,
slug: string,
};
export type UserData = {
id: string,
avatar: string | null,
email: string,
is_active: boolean,
name: string,
};
