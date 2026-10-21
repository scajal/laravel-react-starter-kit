<?php

declare(strict_types=1);

namespace App\Modules\Administration\Panels\Administration\Resources\Users\RelationManagers;

use App\Modules\Administration\Panels\Administration\Resources\Teams\TeamResource;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Team;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TeamsRelationManager extends RelationManager
{
    protected static ?string $inverseRelationship = 'users';

    protected static string $relationship = 'teams';

    /**
     * Get the title of the relation manager.
     */
    #[\Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('Teams');
    }

    /**
     * Configure the relation manager's table.
     */
    #[\Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns($this->getColumns())
            ->headerActions($this->getHeaderActions())
            ->recordActions($this->getRecordActions());
    }

    /**
     * Get the columns for the relation manager's table.
     *
     * @return array<int, \Filament\Tables\Columns\TextColumn>
     */
    protected function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->weight(FontWeight::Medium)
                ->searchable(isIndividual: true),
            Tables\Columns\TextColumn::make('membership.role')
                ->label(trans('Role'))
                ->badge(),
        ];
    }

    /**
     * Get the header actions for the relation manager's table.
     *
     * @return array<\Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\AttachAction::make()
                ->modalHeading(trans('filament-actions::attach.single.modal.heading', ['label' => trans('team')]))
                ->modalWidth(Width::Medium)
                ->preloadRecordSelect()
                ->attachAnother(false)
                ->schema(fn (Actions\AttachAction $action): array => [
                    $action->getRecordSelect(),
                    Forms\Components\Select::make('role')
                        ->placeholder(trans('Select a role'))
                        ->default(Role::Member)
                        ->options(Role::class)
                        ->enum(Role::class)
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->after(function (array $data): void {
                    /** @var \App\Modules\Core\Identity\Models\User $user */
                    $user = $this->getOwnerRecord();

                    /** @var \App\Modules\Core\Identity\Models\Team $team */
                    $team = Team::query()->findOrFail($data['recordId']);

                    // A user who belongs to no team yet lands on
                    // the one they were just attached to.
                    if (! $user->currentTeam) {
                        $user->switchTeam($team);
                    }
                }),
        ];
    }

    /**
     * Get the row actions for the relation manager's table.
     *
     * @return array<\Filament\Actions\ActionGroup>
     */
    protected function getRecordActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\ActionGroup::make([
                    Actions\Action::make('view')
                        ->icon(LucideIcon::View)
                        ->url(fn (Team $record): string => TeamResource::getUrl('view', ['record' => $record])),
                ])->dropdown(false),
                Actions\ActionGroup::make([
                    Actions\DetachAction::make(),
                ])->dropdown(false),
            ]),
        ];
    }

    /**
     * Indicate if the relation manager is read-only.
     */
    #[\Override]
    public function isReadOnly(): bool
    {
        return false;
    }
}
