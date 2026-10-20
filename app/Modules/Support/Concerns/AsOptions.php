<?php

declare(strict_types=1);

namespace App\Modules\Support\Concerns;

use Filament\Support\Contracts\HasLabel;

/**
 * Builds select options from backed enum cases (value + label).
 *
 * @phpstan-require-implements \BackedEnum
 * @phpstan-require-implements HasLabel
 *
 * @method static array<int, static> cases()
 */
trait AsOptions
{
    /**
     * Get the options for the enum as a list of value/label pairs, the shape
     * the Inertia pages render. Defaults to every case sorted by label; pass
     * a subset of cases and/or disable the sort to keep the given order.
     *
     * @param  array<int, static>|null  $cases
     * @return array<int, array{value: string, label: string}>
     */
    public static function asOptions(?array $cases = null, bool $sort = true): array
    {
        /** @var array<int, array{value: string, label: string}> $options */
        $options = collect($cases ?? self::cases())
            ->map(fn (\BackedEnum&HasLabel $case): array => [
                'value' => (string) $case->value,
                'label' => $case->getLabel(),
            ])
            ->when($sort, fn ($options) => $options->sortBy('label'))
            ->values()
            ->toArray();

        return $options;
    }

    /**
     * Get the enum as a value => label map, the shape Filament selects take.
     * Defaults to every case in declaration order.
     *
     * @param  array<int, static>|null  $cases
     * @return array<string, string>
     */
    public static function asLabels(?array $cases = null): array
    {
        /** @var array<string, string> $labels */
        $labels = collect($cases ?? self::cases())
            ->mapWithKeys(fn (\BackedEnum&HasLabel $case): array => [(string) $case->value => $case->getLabel()])
            ->all();

        return $labels;
    }
}
