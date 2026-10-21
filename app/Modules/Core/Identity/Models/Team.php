<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Models;

use App\Modules\Support\Concerns\DefinesKeyType;
use App\Modules\Support\Concerns\HasActivation;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Contracts\HasTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property string $id
 * @property bool $is_active
 * @property string $name
 * @property string $slug
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Audit\Models\Activity> $activitiesAsSubject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Identity\Models\Membership> $memberships
 * @property-read \App\Modules\Core\Identity\Models\Membership|null $membership
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Identity\Models\User> $users
 *
 * @method static \Database\Factories\Modules\Core\Identity\Models\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\Team isActive(string|bool $value = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\Team query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['name', 'slug'])]
class Team extends Model implements HasTimeline
{
    /** @use \App\Modules\Support\Concerns\DefinesKeyType<string> */
    use DefinesKeyType;

    use HasActivation;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\Modules\Core\Identity\Models\TeamFactory> */
    use HasFactory;

    use HasUlids;
    use InteractsWithTimeline;
    use LogsActivity;

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Callback executed when a team is
        // being created.
        static::creating(function (self $team): void {
            if (blank($team->slug)) {
                $team->slug = self::generateSlug($team->name);
            }

            // Ensure the slug is lowercase.
            $team->slug = Str::lower($team->slug);
        });

        // Callback executed when a team is
        // being updated.
        static::updating(function (self $team): void {
            if (blank($team->slug) || ($team->isDirty('name') && ! $team->isDirty('slug'))) {
                $team->slug = self::generateSlug($team->name, $team->getKey());
            }

            $team->slug = Str::lower($team->slug);
        });
    }

    /**
     * Generate a unique slug for the team
     * off the name.
     */
    protected static function generateSlug(string $name, int|string|null $exclude = null): string
    {
        // Generate a default slug based on the name.
        $default = Str::slug($name);

        // Build the query to find a unique slug for
        // the team.
        $isTaken = fn (string $slug): bool => static::query()
            ->where('slug', $slug)
            ->when(filled($exclude), fn (Builder $query): Builder => $query->whereKeyNot($exclude))
            ->exists();

        // If the default slug is not taken, return it.
        if (! $isTaken($default)) {
            return $default;
        }

        // If the default slug is taken, generate a new one.
        do {
            $candidate = "{$default}-".Str::lower(Str::random(6));
        } while ($isTaken($candidate));

        return $candidate;
    }

    /**
     * Get the activity log options for the user.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * Get the memberships of the organization.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Modules\Core\Identity\Models\Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get the timeline for the organization's activity log.
     */
    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)
            ->fromActivityLog();
    }

    /**
     * Get the users that belong to the team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Modules\Core\Identity\Models\User, $this, \App\Modules\Core\Identity\Models\Membership, 'membership'>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, new Membership()->getTable(), $this->getForeignKey(), new User()->getForeignKey())
            ->as('membership')
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
