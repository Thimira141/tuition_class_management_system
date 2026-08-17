<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Classroom;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classroom = Classroom::factory()->create();

        return [
            'attendance_session_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'title' => 'Attendance Session - ' . now() . ' - ' . $classroom->class_code,
            'class_id' => $classroom->id,
            'session_year' => $classroom->payment_method == 'monthly' ? now()->format('Y') : null,
            'session_month' => $classroom->payment_method == 'monthly' ? rand(1, 12) : null,
            'start_datetime' => now(),
            'end_datetime' => now()->addMinutes(120),
            // 'closed_at' => '',
        ];
    }
}
