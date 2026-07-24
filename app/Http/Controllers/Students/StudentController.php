<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // index() → returns JSON for DataTables (listing API).
    public function index()
    {
    }


    /** store() → insert new student.
     * Summary of store
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
        // store data via model
        try {
            $student = Student::create([
                'name' => $validated['student__name'],
                'nic' => $validated['student__nic'],
                'dob' => $validated['student__dob'],
                'joined_at' => $validated['student__joined_at'],
                'email' => $validated['student__email'],
                'tel' => $validated['student__tel'],
                'address' => $validated['student__address'],
                'remarks' => $validated['student__remarks'],
                'guardian_id' => $validated['student__guardian_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
        // store file
        if (!empty($validated['student__cover_img'])) {
            try {
                $file = $validated['student__cover_img'];
                $extension = $file->getClientOriginalExtension();

                // build path using student_code
                $filepath = (string) 'students/' . $student->student_code . '.' . $extension;

                // store file in storage/app/public/students
                $file->storeAs('students', $student->student_code . '.' . $extension, 'public');

                // update student record with filepath
                $student->update([
                    'cover_img' => $filepath
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File upload failed.',
                    'error' => $e->getMessage()
                ], 200);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Store Success!',
            'student' => collect($student->toArray())->mapWithKeys(fn ($value, $key) => ["student__{$key}" => $value]), //  add student__ prefix
        ], 200);

    }

    /**
     * update() → update student.
     * @param Request $request
     * @param int|string $student_code
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function update(Request $request, int|string $student_code)
    {
        // get student from model
        $student = Student::where('student_code', $student_code)->firstOrFail();
        // validate data
        $validate = $this->ValidateData($request, $student->id);
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
            // Update scalar fields
            $student->fill(
                collect($validated)->except('student__cover_img')->toArray()
            );
            // Handle cover image replacement
            if (!empty($validated['student__cover_img'])) {
                try {
                    if ($student->cover_img && Storage::disk('public')->exists($student->cover_img)) {
                        Storage::disk('public')->delete($student->cover_img);
                    }

                    $extension = $validated['student__cover_img']->getClientOriginalExtension();
                    $path = $validated['student__cover_img']
                        ->storeAs('students', $student->student_id . '.' . $extension, 'public');

                    $student->cover_img = $path;
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File upload failed.',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
            // save model
            $student->save();
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
     * destroy() → soft delete student.
     * @param string $student_code
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function destroy(string $student_code)
    {
        try {
            // find model for students.student_code
            $student = Student::withTrashed()->where('student_code', $student_code)->firstOrFail();
            if ($student->trashed()) {
                // restore if already soft deleted
                $student->restore();
                $message = 'Student restored successfully!';
            } else {
                // soft delete if active
                $student->delete();
                $message = 'Student deleted successfully!';
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

    // show() → retrieve single student record.
    public function show(string $student_code)
    {
        // get student from model
        $student = Student::where('student_code', $student_code)->firstOrFail();
        // todo compute other related info
        return response()->json([
            'status' => 'success',
            'data' => ['student' => $student]
        ]);
    }

    /**
     * return json response for tom-select guardians search
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function AJAX_GUARDIANS_INDEX_TS(Request $request)
    {
        $query = Guardian::query();

        if ($search = $request->input('q', '')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('nic', 'like', "%{$search}%");
        }

        // todo: add a prefix 'guardian__'
        $guardians = $query->limit(10)->get(['id', 'guardian_code', 'name', 'nic']);

        return response()->json($guardians);
    }


    /**
     * validate form data using given rules
     * @param Request $request
     * @param int|string|bool $id <table> PK
     * @return \Illuminate\Validation\Validator
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    private function ValidateData(Request $request, int|string|bool $id = false)
    {
        return Validator::make($request->all(), [
            'student__cover_img' => [
                'nullable',
                \Illuminate\Validation\Rules\File::image()->max('2mb')
            ],
            'student__name' => [
                'string',
                'required',
                'min:4',
                'max:200',
                $id !== false
                ? Rule::unique('students', 'name')->ignore($id, 'id') // update mode
                : Rule::unique('students', 'name'), // create mode
            ],
            'student__nic' => [
                'nullable',
                'string',
                'min:5',
                'max:200',
                $id !== false
                ? Rule::unique('students', 'nic')->ignore($id, 'id') // update mode
                : Rule::unique('students', 'nic'), // create mode
            ],
            'student__dob' => [
                'date',
                'required',
            ],
            'student__joined_at' => [
                'date',
                'required',
            ],
            'student__email' => [
                'required',
                'email',
                'max:256',
                $id !== false
                ? Rule::unique('students', 'email')->ignore($id, 'id') // update mode
                : Rule::unique('students', 'email'), // create mode
            ],
            'student__tel' => [
                'required',
                'min:10',
                'max:15',
                'string',
                'regex:/^(\+?[0-9]+|0[0-9]+)$/',
                $id !== false
                ? Rule::unique('students', 'tel')->ignore($id, 'id') // update mode
                : Rule::unique('students', 'tel'), // create mode
            ],
            'student__address' => [
                'string',
                'required',
                'min:4',
                'max:200',
            ],
            'student__remarks' => [
                'string',
                'nullable',
                'min:4',
                'max:200',
            ],
            'student__guardian_id' => [
                'required',
                'exists:guardians,id'
            ],
        ]);
    }

}
