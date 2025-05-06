<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $department = Department::where('status', 1)->get()->toArray();
        if ($department == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Department data fetched successfully!',
            'data' => $department,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_name' => 'required|max:255',
            'department_short_name' => 'required|max:50|unique:departments,department_short_name',
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
            'created_by' => null,
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dept = Department::where('status', 1)->where('id', $id)->first();
        if (!$dept) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Department data fetched successfully!',
            'data' => $dept,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateDept = Department::where('status', 1)->find($id);
    
        if (!$updateDept) {
            return response()->json([
                'message' => 'Department not found!',
                'status' => 404
            ], 404);
        }
    
        $updateDept->update([
            'department_name' => $request->input('department_name', $updateDept->department_name),
            'department_short_name' => $request->input('department_short_name', $updateDept->department_short_name),
            'updated_by' => 1,
        ]);
    
        return response()->json([
            'message' => 'Department updated successfully!',
            'status' => 200
        ], 200);
    }
    


    /**
     * Remove the specified resource from storage.
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
        $deptDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Department deleted successfully!',
            'status' => 200
        ]);
    }
}
