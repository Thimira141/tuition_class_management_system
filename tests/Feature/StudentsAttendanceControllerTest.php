<?php

use Tests\TestCase;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

final class StudentsAttendanceControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_test_student_info_show()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendanceSession = AttendanceSession::factory()->create();
        $student = Student::factory()->create();

        $response = $this->getJson(route('attendance.student.show', [
            'attendance_session' => $attendanceSession->attendance_session_code,
            'student' => $student->student_code
        ]));

        $response->assertStatus(200);
        $response->assertJsonPath('data.marking_check.ok', true);
        $response->assertJsonPath('data.student.student_code', $student->student_code);
    }

    #[Test]
    public function it_test_mark_attendance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendanceSession = AttendanceSession::factory()->create();
        $student = Student::factory()->create();

        $response = $this->postJson(route('attendance.student.mark', [
            'attendance_session' => $attendanceSession->attendance_session_code,
            'student' => $student->student_code
        ]));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'message' => 'Student attendance marked successfully!']);
        $this->assertDatabaseHas('students_attendance', [
            'attendance_session_id' => $attendanceSession->id,
            'student_id' => $student->id,
            'present' => true,
        ]);
    }

    #[Test]
    public function it_test_unmark_attendance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendanceSession = AttendanceSession::factory()->create();
        $student = Student::factory()->create();

        $response = $this->postJson(route('attendance.student.unmark', [
            'attendance_session' => $attendanceSession->attendance_session_code,
            'student' => $student->student_code
        ]));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'message' => 'Student attendance unmarked successfully!']);
        $this->assertDatabaseHas('students_attendance', [
            'attendance_session_id' => $attendanceSession->id,
            'student_id' => $student->id,
            'present' => false,
        ]);
    }
}
