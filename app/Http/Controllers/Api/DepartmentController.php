<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a lists of departments.
     */
    public function index()
    {
        $department = Department::where('status', 1)->select('id', 'department_name', 'department_short_name', 'created_by', 'updated_by')->get()->toArray();
        if ($department == []) {
            return response()->json([
                'message' => 'No Data found!',
                'data' => [],
                'status' => 200
            ], 200);
        }
        return response()->json([
            'message' => 'Department data fetched successfully!',
            'data' => $department,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|max:255',
            'department_short_name' => [
                'required',
                Rule::unique('departments', 'department_short_name')
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdDept = Department::create([
            'department_name' => $request->input('department_name'),
            'department_short_name' => $request->input('department_short_name'),
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        if (!$createdDept) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ], 500);
        }
        return response()->json([
            'message' => 'Department created successfully!!',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show(string $id)
    {
        $dept = Department::where('status', 1)->where('id', $id)->select('id', 'department_name', 'department_short_name', 'created_by', 'updated_by')->first();
        if (!$dept) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $dept = json_decode(json_encode($dept), true);
        return response()->json([
            'message' => 'Department data fetched successfully!',
            'data' => $dept,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|max:255',
            'department_short_name' => [
                'required',
                'max:50',
                Rule::unique('departments', 'department_short_name')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $updateDept = Department::where('status', 1)->select('id', 'department_name', 'department_short_name', 'created_by', 'updated_by')->first($id);

        if (!$updateDept) {
            return response()->json([
                'message' => 'Department not found!',
                'status' => 404
            ], 404);
        }
        $authId = Auth::id();
        if ($updateDept['updated_by'] != null) {
            $updated_by_data = json_decode($updateDept['updated_by'], true);
            if (end($updated_by_data) == $authId) {
                $updated_by_data = json_encode($updated_by_data);
            } else {
                $updated_by_data[] = $authId;
                $updated_by_data = json_encode($updated_by_data);
            }
        } else {
            $updated_by_data[] = $authId;
            $updated_by_data = json_encode($updated_by_data);
        }

        $updateDept->update([
            'department_name' => $request->input('department_name'),
            'department_short_name' => $request->input('department_short_name'),
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Department updated successfully!',
            'status' => 200
        ], 200);
    }



    /**
     * Remove the specified department from storage.
     */
    public function destroy(string $id)
    {
        $deptDelete = Department::where('id', $id)->where('status', 1)->first();

        if (!$deptDelete) {
            return response()->json([
                'message' => 'Department not found!',
                'status' => 404
            ], 404);
        }
        $authId = Auth::id();
        if ($deptDelete->updated_by != null) {
            $updated_by_data = json_decode($deptDelete['updated_by'], true);
            if (end($updated_by_data) == $authId) {
                $updated_by_data = json_encode($updated_by_data);
            } else {
                $updated_by_data[] = $authId;
                $updated_by_data = json_encode($updated_by_data);
            }
        } else {
            $updated_by_data[] = $authId;
            $updated_by_data = json_encode($updated_by_data);
        }
        $deptDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Department deleted successfully!',
            'status' => 200
        ]);
    }
}
