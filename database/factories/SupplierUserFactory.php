<?php

namespace Database\Factories;

use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Models\SupplierUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Supplier\Models\SupplierUser>
 */
class SupplierUserFactory extends Factory
{
    protected $model = SupplierUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
