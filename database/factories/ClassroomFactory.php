<?php

namespace Database\Factories;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
    */
    public function definition(): array
    {
        return [
            'class_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'name'       => $this->faker->words(3, true), // e.g. "Grade 10 Science"
            'grade'      => $this->faker->numberBetween(1, 13),
            'remarks'    => $this->faker->sentence(),
        ];
    }
}
