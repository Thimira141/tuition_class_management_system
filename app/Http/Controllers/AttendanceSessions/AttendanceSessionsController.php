<?php

namespace App\Http\Controllers\AttendanceSessions;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\PrefixKeys;
use App\Traits\StripsPrefixes;
use Yajra\DataTables\Facades\DataTables;

class AttendanceSessionsController extends Controller
{
    /**
     * index() → returns JSON for DataTables (listing API).
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function index(Request $request)
    {
        $query = AttendanceSession::query()->select([
            'attendance_sessions.title as attendance_sessions__title',
            'attendance_sessions.session_year as attendance_sessions__session_year',
            'attendance_sessions.session_month as attendance_sessions__session_month',
            'attendance_sessions.start_datetime as attendance_sessions__start_datetime',
            'attendance_sessions.end_datetime as attendance_sessions__end_datetime',
            'attendance_sessions.closed_at as attendance_sessions__closed_at',
            'attendance_sessions.class_id as attendance_sessions__class_id',
        ])->with('classroom:id,name');

        if ($request->input('showDeleted', 'active') === 'deleted') {
            // only trashed
            $query->onlyTrashed();
        } elseif ($request->input('showDeleted', 'active') === 'all') {
            // both active + trashed
            $query->withTrashed();
        }

        return DataTables::of($query)
            ->addColumn('classroom__name', fn($row) => $row->classroom->name ?? 'DELETED')
            ->make(true);
    }

    /**
     * store() → insert new attendance session.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function store(Request $request)
    {
        // validate data
        $validate = $this->ValidateData($request);
        // handle validate error
        if ($validate->fails()) {
            return response()->json([
                'status' => 'validateFail',
                'message' => 'Data Validate Failed!',
                'errorBag' => $validate->errors()
            ], 422);
        }
        // get validated data
        $validated = $validate->validated();
        // check can this session create/update
        $check = $this->checkSessionCreateAllowed($validated);
        if (!$check['ok']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session create denied!',
                'error' => $check['message'],
            ], 422);
        }
        // store data via model
        try {
            $attendanceSession = AttendanceSession::create(StripsPrefixes::stripPrefix($validated, 'attendance_sessions__'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Store Success!',
            'data' => PrefixKeys::prefixKeys($attendanceSession->toArray(), 'attendance_sessions__'), // add attendance_sessions__ prefix
        ], 200);
    }

    /**
     * update() → update attendance session.
     * @param Request $request
     * @param AttendanceSession $attendanceSession
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function update(Request $request, AttendanceSession $attendanceSession)
    {
        // validate data
        $validate = $this->ValidateData($request, $attendanceSession->id);
        // handle validate error
        if ($validate->fails()) {
            return response()->json([
                'status' => 'validateFail',
                'message' => 'Data Validate Failed!',
                'errorBag' => $validate->errors()
            ], 422);
        }
        // get validated data
        $validated = $validate->validated();
        // check can this session create/update
        $check = $this->checkSessionCreateAllowed($validated, $attendanceSession);
        if (!$check['ok']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session create denied!',
                'error' => $check['message'],
            ], 422);
        }
        try {
            // Update scalar fields
            $attendanceSession->fill(StripsPrefixes::stripPrefix($validated, 'attendance_sessions__'));
            // save model
            $attendanceSession->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Data Update Success!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data update failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * destroy() → soft delete attendance session.
     * @param AttendanceSession $attendanceSession
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function destroy(AttendanceSession $attendanceSession)
    {
        try {
            if ($attendanceSession->trashed()) {
                // restore if already soft deleted
                $attendanceSession->restore();
                $message = 'Attendance Session restored successfully!';
            } else {
                // soft delete if active
                $attendanceSession->delete();
                $message = 'Attendance Session deleted successfully!';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * show() → retrieve single attendance session record.
     * @param AttendanceSession $attendanceSession
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function show(AttendanceSession $attendanceSession)
    {
        return response()->json([
            'status' => 'success',
            'attendance_session' => PrefixKeys::prefixKeys($attendanceSession->toArray(), 'attendance_sessions__')
        ], 200);
    }

    /**
     * close the given attendance session
     * @param AttendanceSession $attendanceSession
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function close_session(AttendanceSession $attendanceSession)
    {
        return $this->setAttendanceSessionClosedAt($attendanceSession, now(), 'Attendance session closed successfully.');
    }

    /**
     * reopen the given attendance session
     * @param AttendanceSession $attendanceSession
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function reopen_session(AttendanceSession $attendanceSession)
    {
        return $this->setAttendanceSessionClosedAt($attendanceSession, null, 'Attendance session reopened successfully.');
    }

    /**
     * set AttendanceSession->closed_at value null|now()
     * @param AttendanceSession $attendanceSession
     * @param \Carbon\Carbon|null $closed_at
     * @param string $successMessage
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    private function setAttendanceSessionClosedAt(AttendanceSession $attendanceSession, \Carbon\Carbon|null $closed_at, string $successMessage = 'Data update success!')
    {
        try {
            $attendanceSession->closed_at = $closed_at;
            $attendanceSession->save();

            return response()->json([
                'status' => 'success',
                'message' => $successMessage,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data update failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * validate form data using given rules
     * @param Request $request
     * @param int|string|bool $id
     * @return \Illuminate\Validation\Validator
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    private function ValidateData(Request $request, int|string|bool $id = false)
    {
        return Validator::make($request->all(), [
            'attendance_sessions__title' => [
                'string',
                'required',
                'min:4',
                'max:200',
                $id !== false
                ? Rule::unique('attendance_sessions', 'title')->ignore($id, 'id') // update mode
                : Rule::unique('attendance_sessions', 'title'), // create mode
            ],
            'attendance_sessions__class_id' => ['required', 'exists:classes,id'],

            // Year/month required only if payment method is monthly
            'attendance_sessions__session_year' => [
                'required_if:classroom__payment_method,monthly',
                'nullable',
                'integer',
                'digits:4',
                'min:2000',
                'max:2100',
            ],
            'attendance_sessions__session_month' => [
                'required_if:classroom__payment_method,monthly',
                'nullable',
                'integer',
                'between:1,12',
            ],

            'attendance_sessions__start_datetime' => ['required', 'date'],
            'attendance_sessions__end_datetime' => [
                'required',
                'date',
                'after_or_equal:attendance_sessions__start_datetime',
            ],

            // Optional: closed_at should be a valid date if provided
            'attendance_sessions__closed_at' => ['nullable', 'date'],

            // IMPORTANT For test cases only — disable in production
            // 'attendance_sessions__attendance_session_code' => 'sometimes|string|unique:attendance_sessions,attendance_session_code'
        ]);
    }

    /**
     * enforcing classroom date ranges and preventing overlapping sessions
     * @param array $validated
     * @param AttendanceSession|bool $attendanceSession
     * @return array{message: string, ok: bool}
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    private function checkSessionCreateAllowed(array $validated, AttendanceSession|bool $attendanceSession = false)
    {
        // 1. Classroom date range
        $classroom = Classroom::find($validated['attendance_sessions__class_id'], ['start_date', 'end_date']);
        if (!$classroom || !now()->between($classroom->start_date, $classroom->end_date)) {
            return [
                'ok' => false,
                'message' => 'Classroom is not active for the current date.'
            ];
        }

        // 2. Prevent overlapping sessions
        $overlap = AttendanceSession::query();

        if (env('ATTENDANCE_STRICT_SINGLE_CLASS', true)) {
            // strict mode → block overlaps globally (no class_id filter)
        } else {
            // normal mode → only block overlaps within the same classroom
            $overlap->where('class_id', $validated['attendance_sessions__class_id']);
        }

        $overlap->where(function ($q) use ($validated) {
            // Case 1 catches overlaps where the existing session starts during the new one
            $q->whereBetween('start_datetime', [
                $validated['attendance_sessions__start_datetime'],
                $validated['attendance_sessions__end_datetime']
            ])
                // Case 2 catches overlaps where the existing session ends during the new one
                ->orWhereBetween('end_datetime', [
                    $validated['attendance_sessions__start_datetime'],
                    $validated['attendance_sessions__end_datetime']
                ])
                // Case 3 catches overlaps where the existing session completely surrounds the new one
                ->orWhere(function ($q2) use ($validated) {
                    $q2->where('start_datetime', '<=', $validated['attendance_sessions__start_datetime'])
                        ->where('end_datetime', '>=', $validated['attendance_sessions__end_datetime']);
                });
        });

        // ignore their own record
        if ($attendanceSession) {
            $overlap->where('id', '!=', $attendanceSession->id);
        }

        if ($overlap->exists()) {
            return [
                'ok' => false,
                'message' => 'Another session overlaps with the given time range.'
            ];
        }

        return [
            'ok' => true,
            'message' => 'Session can be created.'
        ];
    }
}
