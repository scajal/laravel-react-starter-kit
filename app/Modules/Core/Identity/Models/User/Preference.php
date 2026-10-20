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

/**
 * A choice a member made about how the app looks or reads,
 * stored on their account so it follows them across devices.
 *
 * @property \App\Modules\Core\Identity\Enums\User\PreferenceKey $key
 * @property-read \App\MOdules\Core\Identity\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Preference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Preference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Preference query()
 *
 * @mixin \Eloquent
 */
#[Fillable(['key', 'value'])]
#[Table('user_preferences')]
class Preference extends Model
{
    /** @use \App\Modules\Support\Concerns\DefinesKeyType<string> */
    use DefinesKeyType;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\Modules\Core\Identity\Models\User\PreferenceFactory> */
    use HasFactory;

    use HasUlids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'key' => PreferenceKey::class,
        ];
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
