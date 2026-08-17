<?php

namespace App\Http\Controllers\AttendanceSessions;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\StudentAttendance;
use App\Models\Student;
use App\Traits\PrefixKeys;

class StudentsAttendanceController extends Controller
{
    /**
     *  show student instance
     * @param AttendanceSession $attendanceSession
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function show(AttendanceSession $attendanceSession, Student $student)
    {
        $check = $this->checkStudentMarkPossible($attendanceSession, $student);

        return response()->json([
            'status' => 'success',
            'data' => [
                'student' => PrefixKeys::prefixKeys($student->toArray(), 'student__'),
                'attendance' => $attendanceSession->studentsAttendance->toArray(),
                'marking_check' => $check
            ],
            'message' => 'Data found Success!',
        ], 200);
    }

    /**
     * mark student present
     * @param AttendanceSession $attendanceSession
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function markStudentAttendance(AttendanceSession $attendanceSession, Student $student)
    {
        return $this->setStudentAttendance($attendanceSession, $student, true);
    }

    /**
     * mark student not present
     * @param AttendanceSession $attendanceSession
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function unmarkStudentAttendance(AttendanceSession $attendanceSession, Student $student)
    {
        return $this->setStudentAttendance($attendanceSession, $student, false);
    }

    /**
     * mark student present or not
     * @param AttendanceSession $attendanceSession
     * @param Student $student
     * @param bool $present
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    protected function setStudentAttendance(AttendanceSession $attendanceSession, Student $student, bool $present)
    {
        // check student can mark/unmark
        $check = $this->checkStudentMarkPossible($attendanceSession, $student);
        if (!$check['ok']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student did not meet conditions!',
                'error' => $check['message'],
            ], 422);
        }
        try {
            StudentAttendance::updateOrCreate(
                [
                    'attendance_session_id' => $attendanceSession->id,
                    'student_id' => $student->id,
                ],
                [
                    'present' => $present
                ]
            );
            return response()->json([
                'status' => 'success',
                'message' => 'Student attendance ' . ($present ? 'marked' : 'unmarked') . ' successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * enforces the three business rules — session open/closed, time range, and payment check
     * @param AttendanceSession $attendanceSession
     * @param Student $student
     * @return array{message: string, ok: bool}
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    private function checkStudentMarkPossible(AttendanceSession $attendanceSession, Student $student)
    {
        // 1. Check if session is closed
        if ($attendanceSession->closed_at !== null) {
            return [
                'ok' => false,
                'message' => 'Attendance session is already closed.'
            ];
        }

        // 2. Check time range
        $now = now();
        if ($attendanceSession->start_datetime && $now->lt($attendanceSession->start_datetime)) {
            return [
                'ok' => false,
                'message' => 'Attendance cannot be marked before the session start time.'
            ];
        }
        if ($attendanceSession->end_datetime && $now->gt($attendanceSession->end_datetime)) {
            return [
                'ok' => false,
                'message' => 'Attendance cannot be marked after the session end time.'
            ];
        }

        // todo 3. Check payment
        // $paid = \App\Models\Payment::where('student_id', $student->id)
        //     ->where('class_id', $attendanceSession->class_id)
        //     ->when($attendanceSession->session_year && $attendanceSession->session_month, function ($q) use ($attendanceSession) {
        //         $q->where('session_year', $attendanceSession->session_year)
        //             ->where('session_month', $attendanceSession->session_month);
        //     })
        //     ->exists();
        $paid = true;

        if (!$paid) {
            return [
                'ok' => false,
                'message' => 'Student has not paid fees for this session.'
            ];
        }

        // All checks passed
        return [
            'ok' => true,
            'message' => 'Student can be marked.'
        ];
    }
}
