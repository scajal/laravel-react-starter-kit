/**
 * Props merged by `App\Modules\Support\Http\Middleware\HandleInertiaRequests`
 * (and other shared middleware).
 */
export type SharedInertiaProps = {
    /** The colour scheme the user chose; "system" follows the device. */
    appearance: "dark" | "light" | "system";
    /** The locale the app is shown in, e.g. `en`. */
    locale: string;
};
