<?php

declare(strict_types=1);

namespace App\Modules\Core\Audit\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * @property string $id
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $attribute_changes
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property string $description
 * @property string|null $event
 * @property string|null $log_name
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $properties
 * @property string|null $subject_type
 * @property string|null $subject_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $causer
 * @property-read \Illuminate\Database\Eloquent\Model|null $subject
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity forEvent(\Spatie\Activitylog\Enums\ActivityEvent|string $event)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity inLog(array<array-key, string>|string ...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Modules\Core\Audit\Models\Activity query()
 *
 * @mixin \Eloquent
 */
class Activity extends \Spatie\Activitylog\Models\Activity
{
    use HasUlids;
}
