<?php

declare(strict_types=1);

use App\Modules\Core\Identity\Enums\User\PreferenceKey;

test('options list every case sorted by label', function (): void {
    expect(PreferenceKey::asOptions())->toBe([
        ['value' => 'locale', 'label' => 'Language'],
        ['value' => 'appearance', 'label' => 'Theme'],
    ]);
});

test('options keep the given order when sorting is disabled', function (): void {
    expect(PreferenceKey::asOptions(sort: false))->toBe([
        ['value' => 'appearance', 'label' => 'Theme'],
        ['value' => 'locale', 'label' => 'Language'],
    ]);
});

test('options can be limited to a subset of cases', function (): void {
    expect(PreferenceKey::asOptions([PreferenceKey::Appearance]))->toBe([
        ['value' => 'appearance', 'label' => 'Theme'],
    ]);
});

test('labels map every value to its label in declaration order', function (): void {
    expect(PreferenceKey::asLabels())->toBe([
        'appearance' => 'Theme',
        'locale' => 'Language',
    ]);
});

test('labels can be limited to a subset of cases', function (): void {
    expect(PreferenceKey::asLabels([PreferenceKey::Locale]))->toBe([
        'locale' => 'Language',
    ]);
});
