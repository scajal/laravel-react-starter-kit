<?php

declare(strict_types=1);

namespace App\Modules\Administration\Panels\Administration\Resources\Teams\Pages;

use App\Modules\Administration\Panels\Administration\Resources\Teams\TeamResource;
use App\Modules\Core\Identity\Models\Team;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    /**
     * Get the resource's header actions.
     *
     * @return array<\Filament\Actions\ActionGroup>
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('activate')
                    ->action(fn (Team $record): bool => $record->activate())
                    ->visible(fn (Team $record): bool => ! $record->is_active)
                    ->successNotificationTitle(trans('Saved'))
                    ->requiresConfirmation()
                    ->icon(LucideIcon::Check)
                    ->color(Color::Green),
                Actions\Action::make('deactivate')
                    ->action(fn (Team $record): bool => $record->deactivate())
                    ->visible(fn (Team $record): bool => $record->is_active)
                    ->successNotificationTitle(trans('Saved'))
                    ->requiresConfirmation()
                    ->icon(LucideIcon::X)
                    ->color(Color::Red),
            ])->dropdown(false),
            Actions\ActionGroup::make([
                Actions\ViewAction::make(),
            ])->dropdown(false),
            Actions\ActionGroup::make([
                \Relaticle\ActivityLog\Filament\Actions\ActivityLogAction::make(),
            ])->dropdown(false),
        ];
    }
}
