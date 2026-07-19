<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Guardian;

class GuardianController extends Controller
{
    // index() → returns JSON for DataTables (listing API).
    public function index()
    {
    }

    // tomSelect search, return id,code,name,nic fields
    // load data via ajax return cols as request

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
                ], 500);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Store Success!',
            'guardian' => $guardian->toArray(),
            // 'redirect' => route('guardians-view-guardian', (string) $guardian->guardian_code) // todo update the route
        ], 200);
    }

    // update() → update guardian.
    public function update()
    {
    }

    // destroy() → delete guardian.
    public function destroy()
    {
    }

    // show() → retrieve single guardian record.
    public function show()
    {
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
                'regex:/^\+[0-9]+$/',
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
