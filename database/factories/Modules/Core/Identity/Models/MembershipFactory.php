<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Core\Identity\Models;

use App\Modules\Core\Identity\Enums\Team\Role;
use App\Modules\Core\Identity\Models\Membership;
use App\Modules\Core\Identity\Models\Team;
use App\Modules\Core\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Core\Identity\Models\Membership>
 */
class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Modules\Core\Identity\Models\Membership>
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => Role::Member->value,
            new Membership()->team->getForeignKey() => Team::factory(),
            new Membership()->user->getForeignKey() => User::factory(),
        ];
    }

    /**
     * Indicate that the membership is an owner.
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::Owner->value,
        ]);
    }

    /**
     * Indicate that the membership is an admin.
     */
    public function administrator(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::Administrator->value,
        ]);
    }
}
