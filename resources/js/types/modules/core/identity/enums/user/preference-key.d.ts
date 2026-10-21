/**
 * The values each preference accepts, keyed by the preference, as returned
 * by `App\Modules\Core\Identity\Enums\User\PreferenceKey::values()`.
 */
export type PreferenceValues = {
    /** The colour scheme the app is painted in; "system" follows the device. */
    appearance: "dark" | "light" | "system";
    /** The locales the app is translated into (the installed `lang/` locales). */
    locale: "en" | "es";
};

/**
 * The values of `App\Modules\Core\Identity\Enums\User\PreferenceKey`: the
 * choices a member makes about how the app looks and reads.
 */
export type PreferenceKey = keyof PreferenceValues;

/**
 * A colour scheme the app can be painted in.
 */
export type Appearance = PreferenceValues["appearance"];

/**
 * A locale the app can be shown in.
 */
export type Locale = PreferenceValues["locale"];
