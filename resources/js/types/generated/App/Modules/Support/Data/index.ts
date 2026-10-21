import { TeamData, UserData } from "../../Core/Identity/Data/Models";
import { Permission } from "../../Core/Identity/Enums/Team";
import { Appearance, Locale } from "../../Core/Identity/Enums/User";
export type InertiaSharedData = {
    appearance: Appearance;
    authorization: {
        canAccessAdministration: boolean;
        teamPermissions: Permission[];
    };
    current_team: TeamData | null;
    locale: Locale;
    user: UserData | null;
};
