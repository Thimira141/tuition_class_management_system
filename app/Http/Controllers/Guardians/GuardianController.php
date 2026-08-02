<?php

namespace App\Http\Controllers\Guardians;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Guardian;
use App\Traits\StripsPrefixes;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GuardianController extends Controller
{
    /**
     * index() → returns JSON for DataTables (listing API).
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function index(Request $request)
    {
        $query = Guardian::query()->select([
            'guardians.guardian_code as guardian__guardian_code',
            'guardians.cover_img as guardian__cover_img',
            'guardians.name as guardian__name',
            'guardians.tel as guardian__tel',
            'guardians.nic as guardian__nic',
            'guardians.deleted_at as guardian__is_deleted'
        ]);

        if ($request->input('showDeleted', 'active') === 'deleted') {
            // only trashed
            $query->onlyTrashed();
        } elseif ($request->input('showDeleted', 'active') === 'all') {
            // both active + trashed
            $query->withTrashed();
        }

        return DataTables::of($query)
            ->addColumn('guardian__cover_img_url', fn($guardian) => $guardian->guardian__cover_img ? Storage::url($guardian->guardian__cover_img) : asset('images/placeholder-image-member.svg'))
            ->make(true);
    }

    /**
     * store() → insert new guardian.
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
            $guardian = Guardian::create([
                'name' => $validated['guardian__name'],
                'nic' => $validated['guardian__nic'],
                'email' => $validated['guardian__email'],
                'tel' => $validated['guardian__tel'],
                'address' => $validated['guardian__address'],
                'remarks' => $validated['guardian__remarks'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
        // store file
        if (!empty($validated['guardian__cover_img'])) {
            try {
                $file = $validated['guardian__cover_img'];
                $extension = $file->getClientOriginalExtension();

                // build path using guardian_code
                $filepath = (string) 'guardians/' . $guardian->guardian_code . '.' . $extension;

                // store file in storage/app/public/guardians
                $file->storeAs('guardians', $guardian->guardian_code . '.' . $extension, 'public');

                // update guardian record with filepath
                $guardian->update([
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
            'data' => collect($guardian->toArray())->mapWithKeys(fn($value, $key) => ["guardian__{$key}" => $value]), // add guardian__ prefix
        ], 200);
    }

    /**
     * update() → update guardian.
     * @param Request $request
     * @param int|string $guardian_code
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function update(Request $request, int|string $guardian_code)
    {
        // get guardian from model
        $guardian = Guardian::where('guardian_code', $guardian_code)->firstOrFail();
        // validate data
        $validate = $this->ValidateData($request, $guardian->id);
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
            $guardian->fill(StripsPrefixes::stripPrefix(collect($validated)->except('guardian__cover_img')->toArray(), 'guardian__'));
            // Handle cover image replacement
            if (!empty($validated['guardian__cover_img'])) {
                try {
                    if ($guardian->cover_img && Storage::disk('public')->exists($guardian->cover_img)) {
                        Storage::disk('public')->delete($guardian->cover_img);
                    }

                    $extension = $validated['guardian__cover_img']->getClientOriginalExtension();
                    $path = $validated['guardian__cover_img']
                        ->storeAs('guardians', $guardian->guardian_code . '.' . $extension, 'public');

                    $guardian->cover_img = $path;
                } catch (\Exception $e) {
                    // Save scalar fields
                    $guardian->save();
                    // return error!
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File upload failed.',
                        'error' => $e->getMessage()
                    ], 200);
                }
            }
            // save model
            $guardian->save();
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
     * destroy() → soft delete guardian.
     * @param string $guardian_code
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function destroy(string $guardian_code)
    {
        try {
            // find model for guardians.guardian_code
            $guardian = Guardian::withTrashed()->where('guardian_code', $guardian_code)->firstOrFail();
            if ($guardian->trashed()) {
                // restore if already soft deleted
                $guardian->restore();
                $message = 'Guardian restored successfully!';
            } else {
                // soft delete if active
                $guardian->delete();
                $message = 'Guardian deleted successfully!';
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
     * show() → retrieve single guardian record.
     * @param string $guardian_code
     * @return \Illuminate\Http\JsonResponse
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function show(string $guardian_code)
    {
        $guardian = Guardian::where('guardian_code', $guardian_code)->firstOrFail();
        $students = $guardian->students()
            ->withTrashed()
            ->get(['id', 'name', 'student_code', 'cover_img', 'deleted_at'])
            ->map(function ($student) {
                return [
                    'student__name' => $student->name,
                    'student__student_code' => $student->student_code,
                    'student__cover_img' => $student->cover_img, // raw path
                    'student__cover_img_url' => $student->cover_img_url, // accessor
                    'student__deleted_at' => $student->deleted_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'guardian' => collect($guardian->toArray())->mapWithKeys(fn($value, $key) => ["guardian__{$key}" => $value]),
                'students' => $students
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
            'guardian__cover_img' => [
                'nullable',
                \Illuminate\Validation\Rules\File::image()->max('2mb')
            ],
            'guardian__name' => [
                'string',
                'required',
                'min:4',
                'max:200',
                $id !== false
                ? Rule::unique('guardians', 'name')->ignore($id, 'id') // update mode
                : Rule::unique('guardians', 'name'), // create mode
            ],
            'guardian__nic' => [
                'required',
                'string',
                'min:5',
                'max:200',
                $id !== false
                ? Rule::unique('guardians', 'nic')->ignore($id, 'id') // update mode
                : Rule::unique('guardians', 'nic'), // create mode
            ],
            'guardian__email' => [
                'nullable',
                'email',
                'max:256',
                $id !== false
                ? Rule::unique('guardians', 'email')->ignore($id, 'id') // update mode
                : Rule::unique('guardians', 'email'), // create mode
            ],
            'guardian__tel' => [
                'required',
                'min:10',
                'max:15',
                'string',
                'regex:/^(\+?[0-9]+|0[0-9]+)$/',
                $id !== false
                ? Rule::unique('guardians', 'tel')->ignore($id, 'id') // update mode
                : Rule::unique('guardians', 'tel'), // create mode
            ],
            'guardian__address' => [
                'string',
                'required',
                'min:4',
                'max:200',
            ],
            'guardian__remarks' => [
                'string',
                'nullable',
                'min:4',
                'max:200',
            ],
        ]);
    }
}
