<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Core\Identity\Models;

use App\Modules\Core\Identity\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Core\Identity\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Modules\Core\Identity\Models\Team>
     */
    protected $model = Team::class;

    /**
     * Define the model's default state: no Sereno allowance and no model,
     * like every organization until a platform admin sets them.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_active' => true,
            'name' => $name = fake()->unique()->company(),
            'slug' => Str::slug($name),
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
}
