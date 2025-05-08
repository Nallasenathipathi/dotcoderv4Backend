<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAcademics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAcademicController extends Controller
{
    //
    public function index()
    {
        $AcademicData = UserAcademics::where('status', 1)->get()->toArray();
        if ($AcademicData == []) {
            return response()->json([
                'message' => 'No Data found!',
                'data' => [],
                'status' => 200
            ],404);
        }
        return response()->json([
            'message' => 'Academics data fetched successfully!',
            'data' => $AcademicData,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'college_id' => 'required',
            'batch_id' => 'required',
            'department_id' => 'required',
            'section_id' => 'required',
            'academic_marks' => 'required',
            'backlogs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        
        $createdAcademicData = UserAcademics::create([
            'user_id' => $request->input('user_id'),
            'college_id' => $request->input('college_id'),
            'batch_id' => $request->input('batch_id'),
            'department_id' => $request->input('department_id'),
            'section_id' => $request->input('section_id'),
            'academic_marks' => $request->input('academic_marks'),
            'backlogs' => $request->input('backlogs'),
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        if (!$createdAcademicData) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'Academics data created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $AcademicData = UserAcademics::where('status', 1)->where('id', $id)->first();
        if (!$AcademicData) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Academics data fetched successfully!',
            'data' => $AcademicData,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateAcademicData = UserAcademics::where('status', 1)->where('id', $id)->first();

        if (!$updateAcademicData) {
            return response()->json([
                'message' => 'Academics data not found!',
                'status' => 404
            ], 404);
        }

        $authId = Auth::id();
        if ($updateAcademicData['updated_by'] != null) {
            $updated_by_data = json_decode($updateAcademicData['updated_by'], true);
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

        $updateAcademicData->update([
            'user_id' => $request->input('user_id'),
            'college_id' => $request->input('college_id'),
            'batch_id' => $request->input('batch_id'),
            'department_id' => $request->input('department_id'),
            'section_id' => $request->input('section_id'),
            'academic_marks' => $request->input('academic_marks'),
            'backlogs' => $request->input('backlogs'),
            'updated_by' => $updated_by_data ?? null,
        ]);
        $updateAcademicData->save();

        return response()->json([
            'message' => 'College updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $AcademicDelete = UserAcademics::find($id);

        if (!$AcademicDelete) {
            return response()->json([
                'message' => 'Academics data not found!',
                'status' => 404
            ]);
        }
        $AcademicDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Academics data deleted successfully!',
            'status' => 200
        ]);
    }
}
