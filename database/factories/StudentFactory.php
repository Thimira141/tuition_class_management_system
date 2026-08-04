<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
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
            'dob' => $this->faker->date(),
            'joined_at' => $this->faker->date(),
            'email' => $this->faker->email(),
            'tel' => $this->faker->numerify('+94#########'),
            'address' => $this->faker->address(),
            'guardian_id' => Guardian::factory(),
        ];
    }
}
