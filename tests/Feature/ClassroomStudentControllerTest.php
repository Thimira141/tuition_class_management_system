<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ClassroomStudentControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_attached_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();
        $guardian  = Guardian::factory()->create();
        $student   = Student::factory()->create(['guardian_id' => $guardian->id]);

        $classroom->students()->attach($student);

        $response = $this->getJson(route('classrooms.student.ajax.dt.index', [
            'classroom' => $classroom->class_code,
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['student__id' => $student->id]);
    }

    #[Test]
    public function it_attaches_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();
        $guardian  = Guardian::factory()->create();
        $students  = Student::factory()->count(2)->create(['guardian_id' => $guardian->id]);

        $response = $this->postJson(route('classrooms.student.attach', $classroom->class_code), [
            // 'classroom_code' => $classroom->class_code,
            'student_ids'    => $students->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);

        foreach ($students as $student) {
            $this->assertTrue($classroom->students()->where('students.id', $student->id)->exists());
        }
    }

    #[Test]
    public function it_detaches_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();
        $guardian  = Guardian::factory()->create();
        $student   = Student::factory()->create(['guardian_id' => $guardian->id]);

        $classroom->students()->attach($student);

        $response = $this->postJson(route('classrooms.student.detach', $classroom->class_code), [
            // 'classroom_code' => $classroom->class_code,
            'student_ids'    => [$student->id],
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertFalse($classroom->students()->where('students.id', $student->id)->exists());
    }

    #[Test]
    public function it_fails_validation_on_invalid_student_ids()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();

        $response = $this->postJson(route('classrooms.student.attach', $classroom->class_code), [
            // 'classroom_code' => $classroom->class_code,
            'student_ids'    => [999], // non-existent
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['status' => 'validateFail']);
    }
}
