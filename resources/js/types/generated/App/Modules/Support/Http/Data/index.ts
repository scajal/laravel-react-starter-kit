import { Appearance } from '../../../Core/Identity/Enums/User';
import { AuthorizationData, TeamData, UserData } from '../../../Core/Identity/Http/Data';
export type SharedData = {
appearance: Appearance,
authorization: AuthorizationData,
current_team: TeamData | null,
locale: string,
user: UserData | null,
};
