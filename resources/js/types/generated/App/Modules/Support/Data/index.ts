import { UserData, TeamData } from "../../Core/Identity/Data";
import { Permission } from "../../Core/Identity/Enums/Team";
import { Appearance } from "../../Core/Identity/Enums/User";
export type InertiaSharedData = {
    appearance: Appearance;
    authorization: {
        canAccessAdministration: boolean;
        teamPermissions: Permission[];
    };
    current_team: TeamData | null;
    locale: "en" | "es";
    user: UserData | null;
};
