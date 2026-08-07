<?php

namespace App\Http\Controllers\Classrooms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Student;
use App\Traits\StripsPrefixes;
use Yajra\DataTables\Facades\DataTables;

class ClassroomStudentController extends Controller
{
    /**
     * index() → Show students list for a classroom (DataTable AJAX).
     * @param Request $request
     * @param Classroom $classroom
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function index(Request $request, Classroom $classroom)
    {
        $state = $request->input('studentState', 'attached');

        if ($state === 'attached') {
            // Only attached students
            $query = $classroom->students()->getQuery();
        } elseif ($state === 'detached') {
            // Only detached students
            $attachedIds = $classroom->students()->pluck('students.id');
            $query = Student::query()->whereNotIn('id', $attachedIds);
        } else {
            // Default: all students
            $query = Student::query();
        }

        // Apply select once at the end
        $query->select([
            'students.id as student__id',
            'students.name as student__name',
            'students.student_code as student__student_code',
            'students.dob as student__dob',
        ]);

        // keep a memory for attached students
        $attachedIds = $classroom->students()->pluck('students.id')->toArray();

        return DataTables::of($query)
            // Check if this student is attached to the classroom from memory
            ->addColumn('is_attached', fn($student) => \in_array($student->student__id, $attachedIds))
            ->make(true);
    }

    /**
     * attach() → Attach selected students to a classroom.
     * @param Request $request
     * @param Classroom $classroom
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function attach(Request $request, Classroom $classroom)
    {
        // validate data
        $validate = $this->validateData($request);
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
        try {
            $classroom->students()->syncWithoutDetaching($validated['student_ids']);
            return response()->json([
                'status' => 'success',
                'message' => 'Students attached successfully!',
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
     * detach() → Detach selected students from a classroom.
     * @param Request $request
     * @param Classroom $classroom
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function detach(Request $request, Classroom $classroom)
    {
        // validate data
        $validate = $this->validateData($request);
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
        try {
            $classroom->students()->detach($validated['student_ids']);
            return response()->json([
                'status' => 'success',
                'message' => 'Students detached successfully!',
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
     * validate form data using given rules
     * @param Request $request
     * @return \Illuminate\Validation\Validator
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    private function validateData(Request $request)
    {
        return validator($request->all(), [
            'student_ids' => 'array',
            'student_ids.*' => 'exists:students,id',
        ]);
    }
}
