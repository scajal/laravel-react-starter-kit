<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Core\Identity\Models;

use App\Modules\Core\Identity\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Core\Identity\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Modules\Core\Identity\Models\User>
     */
    protected $model = User::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    #[\Override]
    public function configure()
    {
        return $this->afterCreating(function (User $user): void { // @phpstan-ignore return.type
            //
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            new User()->currentTeam()->getForeignKeyName() => null,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => CarbonImmutable::now(),
            'external_id' => 'user_'.Str::ulid(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user has been deactivated by an operator.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the user didn't verify its email.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
