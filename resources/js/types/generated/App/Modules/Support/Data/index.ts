import { Appearance } from '../../Core/Identity/Enums/User';
import { Permission } from '../../Core/Identity/Enums/Team';
export type InertiaSharedData = {
appearance: Appearance,
authorization: {
canAccessAdministration: boolean,
teamPermissions: Permission[],
},
current_team: unknown | null,
locale: 'en' | 'es',
user: unknown | null,
};
