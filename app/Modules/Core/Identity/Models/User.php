<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Modules\Core\Identity\Enums\Team\Permission;
use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Enums\User\PreferenceKey;
use App\Modules\Core\Identity\Models\User\Preference;
use App\Modules\Support\Concerns\DefinesKeyType;
use App\Modules\Support\Concerns\HasActivation;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Contracts\HasTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\CausesActivity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property string $id
 * @property string|null $avatar
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string|null $external_id
 * @property bool $is_active
 * @property string $name
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $current_team_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Audit\Models\Activity> $activitiesAsCauser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Audit\Models\Activity> $activitiesAsSubject
 * @property-read \App\Modules\Core\Identity\Models\Team|null $currentTeam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Identity\Models\Membership> $memberships
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Identity\Models\User\Preference> $preferences
 * @property-read \App\Modules\Core\Identity\Models\Membership|null $membership
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Identity\Models\Team> $teams
 *
 * @method static \Database\Factories\Modules\Core\Identity\Models\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User isActive(string|bool $value = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['avatar', 'email', 'external_id', 'is_active', 'name'])]
#[Hidden(['external_id', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasLocalePreference, HasTimeline
{
    use CausesActivity;

    /** @use \App\Modules\Support\Concerns\DefinesKeyType<string> */
    use DefinesKeyType;

    use HasActivation;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\Modules\Core\Identity\Models\UserFactory> */
    use HasFactory;

    use HasUlids;
    use InteractsWithTimeline;
    use LogsActivity;
    use Notifiable;

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam(Team $team): bool
    {
        return $this->teams()
            ->where($team->getQualifiedKeyName(), $team->getKey())
            ->exists();
    }

    /**
     * Indicate if the user can access the administration panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return App::isLocal();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the user's current team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Modules\Core\Identity\Models\Team, $this>
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get the activity log options for the user.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->logExcept(['external_id', 'remember_token']);
    }

    /**
     * Get the password for the user. Sign-in happens at WorkOS AuthKit, so
     * the application holds no password to compare against.
     */
    #[\Override]
    public function getAuthPassword(): string
    {
        return '';
    }

    /**
     * Determine if the user has the given permission on the team.
     *
     * @codeCoverageIgnore Unreachable until the Permission enum declares its first case.
     */
    public function hasTeamPermission(Team $team, Permission $permission): bool
    {
        return $this->teamRole($team)?->grants($permission) ?? false;
    }

    /**
     * Determine if the given team is the user's current team.
     */
    public function isCurrentTeam(Team $team): bool
    {
        return $team->is($this->currentTeam);
    }

    /**
     * Get the user's memberships, one per team they belong to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Modules\Core\Identity\Models\Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get the value the user stored for the given preference, or
     * null when they never chose one. The preferences are loaded
     * once, so the middleware reading them on every request
     * shares one query.
     */
    public function preference(PreferenceKey $key): ?string
    {
        return $this->preferences->firstWhere('key', $key)?->value;
    }

    /**
     * Get the preferences the user stored.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Modules\Core\Identity\Models\User\Preference, $this>
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(Preference::class);
    }

    /**
     * Get the preferred locale of the user.
     */
    public function preferredLocale(): string
    {
        return $this->preference(PreferenceKey::Locale) ?? App::currentLocale();
    }

    /**
     * Store the given value for the preference, replacing the one
     * the user chose before. The loaded preferences are dropped,
     * so the next read sees the new value.
     */
    public function rememberPreference(PreferenceKey $key, string $value): Preference
    {
        $preference = $this->preferences()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        $this->unsetRelation('preferences');

        return $preference;
    }

    /**
     * Switch to the given team.
     */
    public function switchTeam(Team $team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        // Update the user's current team and persist it.
        $this->currentTeam()
            ->associate($team)
            ->save();

        // Set the default URL parameters for team-based routes.
        URL::defaults(['team' => $team->slug]);

        return true;
    }

    /**
     * Get the user's membership of the given team, or null when
     * they do not belong to it.
     */
    public function teamMembership(Team $team): ?Membership
    {
        return $this->memberships()
            ->whereBelongsTo($team)
            ->first();
    }

    /**
     * Get the user's role on the given team.
     */
    public function teamRole(Team $team): ?Role
    {
        return $this->teamMembership($team)?->role;
    }

    /**
     * Get the teams the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Modules\Core\Identity\Models\Team, $this, \App\Modules\Core\Identity\Models\Membership, 'membership'>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, new Membership()->getTable(), $this->getForeignKey(), new Team()->getForeignKey())
            ->using(Membership::class)
            ->withPivot(['role'])
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get the timeline for the user's activity log.
     */
    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)
            ->fromActivityLog();
    }
}
