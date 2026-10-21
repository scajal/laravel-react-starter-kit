<?php

namespace App\Modules\Core\Identity\Enums\User;

use App\Modules\Support\Concerns\AsOptions;
use Filament\Support\Contracts\HasLabel;

/**
 * The languages the app is translated into.
 *
 * @see \App\Modules\Core\Identity\Enums\User\PreferenceKey::Locale
 */
enum Locale: string implements HasLabel
{
    use AsOptions;

    /**
     * Represents English.
     */
    case English = 'en';

    /**
     * Represents Spanish.
     */
    case Spanish = 'es';

    /**
     * Get the label for the locale, in its own language.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Spanish => 'Español',
        };
    }
}
