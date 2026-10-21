<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Enums\User;

use App\Modules\Support\Concerns\AsOptions;
use Filament\Support\Contracts\HasLabel;

/**
 * The colour schemes the app can be painted in.
 *
 * @see \App\Modules\Core\Identity\Enums\User\PreferenceKey::Appearance
 */
enum Appearance: string implements HasLabel
{
    use AsOptions;

    /**
     * Represents the dark colour scheme.
     */
    case Dark = 'dark';

    /**
     * Represents the light colour scheme.
     */
    case Light = 'light';

    /**
     * Represents the colour scheme the device prefers.
     */
    case System = 'system';

    /**
     * Get the label for the appearance.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Dark => trans('Dark'),
            self::Light => trans('Light'),
            self::System => trans('System'),
        };
    }
}
