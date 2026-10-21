<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\App;

it('groups the discovered resources under the application in the navigation', function (string $locale, string $group, array $items): void {
    asUser();
    App::setLocale($locale);
    Filament::setCurrentPanel('administration');

    $navigation = collect(Filament::getNavigation())
        ->mapWithKeys(fn (NavigationGroup $group): array => [
            (string) $group->getLabel() => collect($group->getItems())->map(fn (NavigationItem $item): string => (string) $item->getLabel())->all(),
        ]);

    expect($navigation->get($group))->toEqualCanonicalizing($items);
})->with([
    'english' => ['en', 'Application', ['Teams', 'Users']],
    'spanish' => ['es', 'Aplicación', ['Equipos', 'Usuarios']],
]);
