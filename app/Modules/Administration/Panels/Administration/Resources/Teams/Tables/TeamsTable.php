<?php

declare(strict_types=1);

namespace App\Modules\Administration\Panels\Administration\Resources\Teams\Tables;

use Filament\Actions;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class TeamsTable
{
    /**
     * Configure the resource's table.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(self::getColumns())
            ->filters(self::getFilters())
            ->recordActions(self::getRecordActions());
    }

    /**
     * Get the table columns.
     *
     * @return array<int, \Filament\Tables\Columns\TextColumn>
     */
    protected static function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->weight(FontWeight::Medium)
                ->searchable(isIndividual: true),
            Tables\Columns\TextColumn::make('is_active')
                ->formatStateUsing(fn (bool $state): string => $state ? trans('Active') : trans('Inactive'))
                ->color(fn (bool $state): array => $state ? Color::Green : Color::Red)
                ->badge(),
        ];
    }

    /**
     * Get the table columns.
     *
     * @return array<int, \Filament\Tables\Columns\TextColumn>
     */
    protected static function getFilters(): array
    {
        return [
            Tables\Filters\TernaryFilter::make('is_active')
                ->native(false),
        ];
    }

    /**
     * Get the row actions for the table.
     *
     * @return array<int, \Filament\Actions\ActionGroup>
     */
    protected static function getRecordActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\ActionGroup::make([
                    Actions\ViewAction::make(),
                    Actions\EditAction::make(),
                ])->dropdown(false),
                Actions\ActionGroup::make([
                    Actions\DeleteAction::make(),
                ])->dropdown(false),
            ]),
        ];
    }
}
