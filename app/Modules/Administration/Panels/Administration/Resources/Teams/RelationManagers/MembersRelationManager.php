<?php

declare(strict_types=1);

namespace App\Modules\Administration\Panels\Administration\Resources\Teams\RelationManagers;

use App\Modules\Administration\Panels\Administration\Resources\Users\UserResource;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\User;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembersRelationManager extends RelationManager
{
    protected static ?string $inverseRelationship = 'teams';

    protected static string $relationship = 'users';

    /**
     * Get the title of the relation manager.
     */
    #[\Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('Members');
    }

    /**
     * Configure the relation manager's table.
     */
    #[\Override]
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
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
            Tables\Columns\TextColumn::make('email')
                ->searchable(isIndividual: true),
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
                ->modalHeading(trans('filament-actions::attach.single.modal.heading', ['label' => trans('user')]))
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
                    /** @var \App\Modules\Core\Identity\Models\Team $team */
                    $team = $this->getOwnerRecord();

                    /** @var \App\Modules\Core\Identity\Models\User $user */
                    $user = User::query()->findOrFail($data['recordId']);

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
                        ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record])),
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
