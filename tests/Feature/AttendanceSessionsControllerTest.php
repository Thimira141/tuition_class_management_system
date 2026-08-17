<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Classroom;
use App\Models\AttendanceSession;
use App\Models\User;
use App\Traits\PrefixKeys;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AttendanceSessionsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_list_attendance_sessions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AttendanceSession::factory()->count(10)->create();

        $response = $this->getJson(route('attendance_sessions.ajax.dt.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'attendance_sessions__title',
                    'attendance_sessions__session_year',
                    'attendance_sessions__session_month',
                    'attendance_sessions__start_datetime',
                    'attendance_sessions__end_datetime',
                    'attendance_sessions__closed_at',
                    'attendance_sessions__class_id',
                    'classroom__name',
                ]
            ]
        ]);
    }

    #[Test]
    public function it_create_attendance_session()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();
        $data = [
            'attendance_sessions__title' => $title = 'Attendance Session - TESTCASE - ' . now() . ' - ' . $classroom->class_code,
            'attendance_sessions__class_id' => $classroom->id,
            'attendance_sessions__session_year' => $classroom->payment_method == 'monthly' ? 2026 : null,
            'attendance_sessions__session_month' => $classroom->payment_method == 'monthly' ? 8 : null,
            'classroom__payment_method' => $classroom->payment_method,
            'attendance_sessions__start_datetime' => now(),
            'attendance_sessions__end_datetime' => now()->addMinutes(120),
            'attendance_sessions__attendance_session_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ];

        $response = $this->postJson(route('attendance_sessions.ajax.store'), $data);
        // dd($data, $response->json(), $response->status());
        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('attendance_sessions', ['title' => $title]);
    }

    #[Test]
    public function it_update_attendance_session()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendanceSession = AttendanceSession::factory()->create();

        $data = $attendanceSession->toArray();
        $data['title'] = $title = "UPDATED-TITLE";
        unset($data['attendance_session_code']);
        $data = PrefixKeys::prefixKeys($data, 'attendance_sessions__')->toArray();

        $response = $this->putJson(route('attendance_sessions.ajax.update', $attendanceSession), $data);
        // dd($response->json(), $response->status(), $data);
        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('attendance_sessions', ['id' => $attendanceSession->id, 'title' => $title]);
    }

    #[Test]
    public function it_delete_attendance_session()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendanceSession = AttendanceSession::factory()->create();

        $response = $this->deleteJson(route('attendance_sessions.ajax.destroy', $attendanceSession));

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseMissing('attendance_sessions', ['attendance_sessions__id' => $attendanceSession->id]);
    }

    #[Test]
    public function it_test_session_overlaps()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // First classroom + session
        $classroom1 = Classroom::factory()->create();
        $data1 = [
            'attendance_sessions__title' => $title1 = 'Attendance Session - TESTCASE - ' . now() . ' - ' . $classroom1->class_code,
            'attendance_sessions__class_id' => $classroom1->id,
            'attendance_sessions__session_year' => $classroom1->payment_method == 'monthly' ? 2026 : null,
            'attendance_sessions__session_month' => $classroom1->payment_method == 'monthly' ? 8 : null,
            'classroom__payment_method' => $classroom1->payment_method,
            'attendance_sessions__start_datetime' => now(),
            'attendance_sessions__end_datetime' => now()->addMinutes(120),
            'attendance_sessions__attendance_session_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ];

        $response1 = $this->postJson(route('attendance_sessions.ajax.store'), $data1);
        $response1->assertStatus(200)->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('attendance_sessions', ['title' => $title1]);

        // Second classroom (only used if strict mode is OFF)
        $classroom2 = Classroom::factory()->create();
        // $classroom2 = $classroom1;

        $data2 = [
            'attendance_sessions__title' => $title2 = 'Attendance Session - TESTCASE N2 - ' . now() . ' - ' . $classroom2->class_code,
            'attendance_sessions__class_id' => $classroom2->id,
            'attendance_sessions__session_year' => $classroom2->payment_method == 'monthly' ? 2026 : null,
            'attendance_sessions__session_month' => $classroom2->payment_method == 'monthly' ? 8 : null,
            'classroom__payment_method' => $classroom2->payment_method,
            'attendance_sessions__start_datetime' => now()->addMinutes($addTime=0),   // increase variable
            'attendance_sessions__end_datetime' => now()->addMinutes(120 + $addTime),
            'attendance_sessions__attendance_session_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        ];

        $response2 = $this->postJson(route('attendance_sessions.ajax.store'), $data2);

        if (env('ATTENDANCE_STRICT_SINGLE_CLASS', false)) {
            // Strict mode ON → overlaps blocked globally
            if ((bool) $addTime) {
                $response2->assertStatus(200)->assertJsonFragment(['status' => 'success']);
                $this->assertDatabaseHas('attendance_sessions', ['title' => $title2]);
            } else {
                $response2->assertStatus(422)->assertJsonFragment([
                    "status" => "error",
                    "message" => "Session create denied!",
                ]);
            }
        } else {
            // Strict mode OFF → overlaps allowed if different classroom
            $response2->assertStatus(200)->assertJsonFragment(['status' => 'success']);
            $this->assertDatabaseHas('attendance_sessions', ['title' => $title2]);
        }
    }
}
