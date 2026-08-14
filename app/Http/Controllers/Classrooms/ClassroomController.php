<?php

namespace App\Http\Controllers\Classrooms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Traits\PrefixKeys;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\StripsPrefixes;
use Yajra\DataTables\Facades\DataTables;

class ClassroomController extends Controller
{
    /**
     * index() → returns JSON for DataTables (listing API).
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function index(Request $request)
    {
        $query = Classroom::query()->select([
            'classes.id as classroom__id',
            'classes.class_code as classroom__class_code',
            'classes.name as classroom__name',
            'classes.grade as classroom__grade',
            'classes.deleted_at as classroom__is_deleted'
        ]);

        if ($request->input('showDeleted', 'active') === 'deleted') {
            // only trashed
            $query->onlyTrashed();
        } elseif ($request->input('showDeleted', 'active') === 'all') {
            // both active + trashed
            $query->withTrashed();
        }

        return DataTables::of($query)
        // count students for each classroom
            ->addColumn('classroom__student_count', fn($class) => (string) Classroom::withTrashed()->find($class->classroom__id)->students()->count())
            ->make(true);
    }

    /**
     * store() → insert new classroom.
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
            $classroom = Classroom::create(StripsPrefixes::stripPrefix($validated, 'classroom__'));
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
            'data' => PrefixKeys::prefixKeys($classroom->toArray(), "classroom__"), // add classroom__ prefix
        ], 200);
    }

    /**
     * update() → update classroom.
     * @param Request $request
     * @param Classroom $classroom
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function update(Request $request, Classroom $classroom)
    {
        // validate data
        $validate = $this->ValidateData($request, $classroom->id);
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
            $classroom->fill(StripsPrefixes::stripPrefix($validated, 'classroom__'));
            // save model
            $classroom->save();
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
     * destroy() → soft delete classroom.
     * @param Classroom $classroom
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function destroy(Classroom $classroom)
    {
        try {
            if ($classroom->trashed()) {
                // restore if already soft deleted
                $classroom->restore();
                $message = 'classroom restored successfully!';
            } else {
                // soft delete if active
                $classroom->delete();
                $message = 'classroom deleted successfully!';
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
     * show() → retrieve single classroom record.
     * @param Classroom $classroom
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function show(Classroom $classroom)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'classroom' => collect($classroom->toArray())->mapWithKeys(fn($value, $key) => ["classroom__{$key}" => $value]),
                'students' => $classroom->students()->withTrashed()->get([
                    'name as student__name',
                    'student_code as student__student_code',
                    'cover_img'
                ])->makeHidden('pivot') // hide pivot cause i don't use this in here
            ]
        ]);
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
            'classroom__name' => [
                'string',
                'required',
                'min:4',
                'max:200',
                $id !== false
                ? Rule::unique('classes', 'name')->ignore($id, 'id') // update mode
                : Rule::unique('classes', 'name'), // create mode
            ],
            'classroom__grade' => [
                'string',
                'required',
                'min:1',
                'max:10',
            ],
            'classroom__remarks' => [
                'string',
                'nullable',
                'min:4',
                'max:200',
            ],
            'classroom__price' => [ // unit LKR
                'required',
                'numeric',
                'min:0',
                'max:99999.99',
            ],
            'classroom__payment_method' => [ // unit minutes
                'required',
                'string',
                'in:once,monthly'
            ],
            'classroom__start_date' => [
                'date',
                'required',
            ],
            'classroom__end_date' => [
                'date',
                'required',
                'after_or_equal:classroom__start_date'
            ],
            //IMPORTANT strictly only for testcases, once tests are over please disable this validation
            // 'classroom__class_code' => 'sometimes|string|unique:classes,class_code',
        ]);
    }
}
