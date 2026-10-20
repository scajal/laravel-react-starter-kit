<?php

declare(strict_types=1);

namespace App\Modules\Administration\Resources;

use Illuminate\Support\Str;

abstract class Resource extends \Filament\Resources\Resource
{
    /**
     * Get the resource model's label.
     */
    #[\Override]
    public static function getModelLabel(): string
    {
        return trans(Str::lower(parent::getModelLabel()));
    }

    /**
     * Get the resource model's plural label.
     */
    #[\Override]
    public static function getPluralModelLabel(): string
    {
        return trans(parent::getPluralModelLabel());
    }

    /**
     * Get the title case for the resource model's label.
     */
    #[\Override]
    public static function getTitleCaseModelLabel(): string
    {
        return trans(Str::lower(self::getModelLabel()));
    }

    /**
     * Get the title case for the resource model's plural label.
     */
    #[\Override]
    public static function getTitleCasePluralModelLabel(): string
    {
        return trans(Str::ucfirst(Str::lower(parent::getTitleCasePluralModelLabel())));
    }
}
