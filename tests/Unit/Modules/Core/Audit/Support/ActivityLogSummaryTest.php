<?php

declare(strict_types=1);

use App\Modules\Core\Audit\Support\ActivityLogSummary;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Relaticle\ActivityLog\Support\ActivityLogDiffRow;
use Relaticle\ActivityLog\Support\ActivityLogOperation;
use Relaticle\ActivityLog\Timeline\TimelineEntry;

/**
 * Build a timeline entry for the given event.
 *
 * @param  array<string, mixed>  $properties
 */
function timelineEntry(string $event, ?Model $subject = null, ?Model $causer = null, array $properties = []): TimelineEntry
{
    return new TimelineEntry(
        id: '01',
        type: 'activity_log',
        event: $event,
        occurredAt: CarbonImmutable::parse('2026-01-15 09:30:00'),
        dedupKey: 'activity_log:01',
        sourcePriority: 0,
        subject: $subject,
        causer: $causer,
        properties: $properties,
    );
}

/**
 * Build an unsaved model holding the given attributes.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @param  class-string<TModel>  $model
 * @param  array<string, mixed>  $attributes
 * @return TModel
 */
function unsaved(string $model, array $attributes = []): Model
{
    return new $model()->forceFill($attributes);
}

it('names the causer of the activity', function (): void {
    $entry = timelineEntry('created', causer: unsaved(User::class, ['name' => 'Ada Lovelace']));

    $summary = ActivityLogSummary::from($entry);

    expect($summary->causerName)->toBe('Ada Lovelace');
});

it('attributes the activity to the system when the causer has no name', function (Closure $causer): void {
    $entry = timelineEntry('created', causer: $causer());

    $summary = ActivityLogSummary::from($entry);

    expect($summary->causerName)->toBe('System');
})->with([
    'no causer' => fn (): ?Model => null,
    'a causer without a name attribute' => fn (): Model => unsaved(Membership::class),
    'a causer with an empty name' => fn (): Model => unsaved(User::class, ['name' => '']),
]);

it('summarises an update of one field and lists its change', function (): void {
    $entry = timelineEntry('updated', unsaved(Team::class, ['name' => 'Globex']), unsaved(User::class, ['name' => 'Ada']), [
        'attributes' => ['name' => 'Globex'],
        'old' => ['name' => 'Acme'],
    ]);

    $summary = ActivityLogSummary::from($entry);

    expect($summary)
        ->operation->toBe(ActivityLogOperation::Updated)
        ->summarySentence->toBe('Ada changed Name')
        ->changedFieldLabels->toBe(['Name'])
        ->diffRows->toEqual([new ActivityLogDiffRow(label: 'Name', old: 'Acme', new: 'Globex')])
        ->hasDiff->toBeTrue();
});

it('counts the fields of an update that changed several', function (): void {
    $entry = timelineEntry('updated', unsaved(Team::class), unsaved(User::class, ['name' => 'Ada']), [
        'attributes' => ['name' => 'Globex', 'is_active' => false],
        'old' => ['name' => 'Acme', 'is_active' => true],
    ]);

    $summary = ActivityLogSummary::from($entry);

    expect($summary)
        ->summarySentence->toBe('Ada changed 2 attributes')
        ->changedFieldLabels->toBe(['Name', 'Is Active']);
});

it('lists a field that was only set or only cleared', function (): void {
    $entry = timelineEntry('updated', unsaved(Team::class), properties: [
        'attributes' => ['name' => 'Globex'],
        'old' => ['slug' => 'acme'],
    ]);

    $summary = ActivityLogSummary::from($entry);

    expect($summary->diffRows)->toEqual([
        new ActivityLogDiffRow(label: 'Name', old: null, new: 'Globex'),
        new ActivityLogDiffRow(label: 'Slug', old: 'acme', new: null),
    ]);
});

it('hides timestamps and internal fields from the changes', function (): void {
    $entry = timelineEntry('updated', unsaved(Team::class, ['name' => 'Acme']), unsaved(User::class, ['name' => 'Ada']), [
        'attributes' => ['_token' => 'secret', 'created_at' => '2026-01-15', 'deleted_at' => null, 'updated_at' => '2026-01-15'],
    ]);

    $summary = ActivityLogSummary::from($entry);

    expect($summary)
        ->summarySentence->toBe('Ada updated Acme')
        ->changedFieldLabels->toBe([])
        ->diffRows->toBe([])
        ->hasDiff->toBeFalse();
});

it('ignores change properties that are not lists of attributes', function (): void {
    $entry = timelineEntry('updated', unsaved(Team::class, ['name' => 'Acme']), properties: [
        'attributes' => 'name',
        'old' => null,
    ]);

    $summary = ActivityLogSummary::from($entry);

    expect($summary)
        ->summarySentence->toBe('System updated Acme')
        ->diffRows->toBe([]);
});

it('summarises other operations with their verb', function (): void {
    $entry = timelineEntry('created', unsaved(Team::class, ['name' => 'Acme']), unsaved(User::class, ['name' => 'Ada']), [
        'attributes' => ['name' => 'Acme'],
    ]);

    $summary = ActivityLogSummary::from($entry);

    expect($summary)
        ->operation->toBe(ActivityLogOperation::Created)
        ->summarySentence->toBe('Ada created Acme')
        ->hasDiff->toBeFalse();
});

it('summarises a custom event with its headline', function (): void {
    $entry = timelineEntry('roleChanged', unsaved(Team::class, ['name' => 'Acme']), unsaved(User::class, ['name' => 'Ada']));

    $summary = ActivityLogSummary::from($entry);

    expect($summary)
        ->operation->toBeNull()
        ->summarySentence->toBe('Ada role changed Acme');
});

it('names the subject after its model when it has no name', function (Closure $subject): void {
    $entry = timelineEntry('deleted', $subject(), unsaved(User::class, ['name' => 'Ada']));

    $summary = ActivityLogSummary::from($entry);

    expect($summary->summarySentence)->toBe('Ada deleted membership');
})->with([
    'a subject without a name attribute' => fn (): Model => unsaved(Membership::class),
    'a subject with an empty name' => fn (): Model => unsaved(Membership::class, ['name' => '']),
]);

it('refers to a missing subject as this record', function (): void {
    $entry = timelineEntry('deleted', causer: unsaved(User::class, ['name' => 'Ada']));

    $summary = ActivityLogSummary::from($entry);

    expect($summary->summarySentence)->toBe('Ada deleted this record');
});
