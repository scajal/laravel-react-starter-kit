<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\User\Locale;
use LaravelLang\Locales\Facades\Locales;

test('the locales match the installed languages', function (): void {
    expect(array_column(Locale::cases(), 'value'))
        ->toEqualCanonicalizing(Locales::installed()->pluck('code')->all());
});
