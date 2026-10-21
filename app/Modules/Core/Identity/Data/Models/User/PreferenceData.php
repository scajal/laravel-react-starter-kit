<?php

namespace App\Modules\Core\Identity\Data\Models\User;

use App\Modules\Core\Identity\Enums\User\Appearance;
use App\Modules\Core\Identity\Enums\User\Locale;
use App\Modules\Core\Identity\Enums\User\PreferenceKey;
use App\Modules\Core\Identity\Models\User;
use Illuminate\Support\Facades\App;

class PreferenceData extends \Spatie\LaravelData\Data
{
    /**
     * Create an instance of the class.
     */
    public function __construct(
        /**
         * The colour scheme the user chose, or "system" when
         * they left the choice to the device.
         */
        public Appearance $appearance,

        /**
         * The language the user chose, or the current one when
         * they never chose one.
         */
        public Locale $locale,
    ) {}

    /**
     * Create a data's instance from the provided user.
     */
    public static function fromUser(User $user): self
    {
        // Callback for getting the preference by
        // the provided key.
        $preference = fn (PreferenceKey $key): ?string => $user->preferences->firstWhere('key', $key)?->value;

        return new self(
            appearance: Appearance::tryFrom($preference(PreferenceKey::Appearance) ?? '') ?? Appearance::System,
            locale: Locale::tryFrom($preference(PreferenceKey::Locale) ?? '') ?? Locale::from(App::currentLocale()),
        );
    }
}
