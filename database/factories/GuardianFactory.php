<?php

namespace Database\Factories;

use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guardian>
 */
class GuardianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guardian_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->name(),
            'nic' => $this->faker->numerify('############V'),
            'email' => $this->faker->email(),
            'tel' => $this->faker->numerify('+94#########'),
            'address' => $this->faker->address(),
        ];
    }
}
