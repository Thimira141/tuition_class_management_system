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
            'name' => $this->faker->name(),
            'nic' => $this->faker->numerify('############V'),
            'email' => $this->faker->email(),
            'tel' => $this->faker->numerify('+94#########'),
            'address' => $this->faker->address(),
        ];
    }
}
