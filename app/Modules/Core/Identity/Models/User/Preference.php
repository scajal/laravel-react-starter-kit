<?php

declare(strict_types=1);

namespace App\Modules\Core\Identity\Models\User;

use App\Modules\Core\Identity\Enums\User\PreferenceKey;
use App\Modules\Core\Identity\Models\User;
use App\Modules\Support\Concerns\DefinesKeyType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Relaticle\ActivityLog\Concerns\InteractsWithTimeline;
use Relaticle\ActivityLog\Contracts\HasTimeline;
use Relaticle\ActivityLog\Timeline\TimelineBuilder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * A choice a user made about how the app looks, reads or behaves,
 * stored on their account so it follows them across devices.
 *
 * @property string $id
 * @property \App\Modules\Core\Identity\Enums\User\PreferenceKey $key
 * @property string $user_id
 * @property string $value
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Core\Monitoring\Models\Activity> $activitiesAsSubject
 * @property-read \App\Modules\Core\Identity\Models\User $user
 *
 * @method static \Database\Factories\Modules\Core\Identity\Models\User\PreferenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User\Preference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User\Preference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Identity\Models\User\Preference query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['key', 'value'])]
#[Table('user_preferences')]
class Preference extends Model implements HasTimeline
{
    /** @use \App\Modules\Support\Concerns\DefinesKeyType<string> */
    use DefinesKeyType;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\Modules\Core\Identity\Models\User\PreferenceFactory> */
    use HasFactory;

    use HasUlids;
    use InteractsWithTimeline;
    use LogsActivity;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'key' => PreferenceKey::class,
        ];
    }

    /**
     * Get the activity log options for the preference.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * Get the timeline for the preference's activity log.
     */
    public function timeline(): TimelineBuilder
    {
        return TimelineBuilder::make($this)
            ->fromActivityLog();
    }

    /**
     * Get the user the preference belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Modules\Core\Identity\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
