<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\User\Appearance;
use Illuminate\Support\Facades\App;

test('each appearance has a translated label', function (Appearance $appearance, string $english, string $spanish): void {
    expect($appearance->getLabel())->toBe($english);

    App::setLocale('es');

    expect($appearance->getLabel())->toBe($spanish);
})->with([
    'dark' => [Appearance::Dark, 'Dark', 'Oscuro'],
    'light' => [Appearance::Light, 'Light', 'Claro'],
    'system' => [Appearance::System, 'System', 'Sistema'],
]);
