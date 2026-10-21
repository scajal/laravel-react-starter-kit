/**
 * An option built by `App\Modules\Support\Concerns\AsOptions::asOptions()`,
 * one per enum case.
 */
export type Option<TValue extends string = string> = {
    /** The backing value of the enum case. */
    value: TValue;
    /** The translated label of the enum case. */
    label: string;
};
