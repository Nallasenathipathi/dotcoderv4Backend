<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAcademics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CollegeAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collegeAdmin = User::where('status', 1)->where('role', 5)->select('id', 'name', 'email', 'password', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')->get()->toArray();

        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $collegeAdmin,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'password' => 'required',
            'college_id' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('status', 1);
                }),
            ],
            'profile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdTrainer = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'dob' => $request->input('dob'),
            'gender' => $request->input('gender'),
            'gender' => $request->input('gender'),
            'role' => 5,
            'contact_number' => $request->input('contact_number'),
            'profile' => $request->input('profile'),
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        $createdAcademics = UserAcademics::create([
            'user_id' => $createdTrainer->id,
            'college_id' => $request->input('college_id'),
            'status' => 1
        ]);
        if (!$createdTrainer && !$createdAcademics) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ], 500);
        }
        return response()->json([
            'message' => 'Data created successfully!!',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $collegeAdmin = User::where('status', 1)->select('id', 'name', 'email', 'password', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')->where('id', $id)->first();

        if (!$collegeAdmin) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $collegeAdmin = json_decode(json_encode($collegeAdmin), true);
        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $collegeAdmin,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'password' => 'required',
            'college_id' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->where(function ($query) {
                    return $query->where('status', 1);
                }),
            ],
            'profile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $authId = Auth::id();

        $updateUser = User::where('status', 1)->select('id', 'updated_by')->where('id', $id)->first();
        $userAcademics = UserAcademics::where('user_id', $updateUser->id)->where('status', 1)->first();
        if ($updateUser->updated_by != null) {
            $updated_by_data = json_decode($updateUser['updated_by'], true);
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

        if (!$updateUser) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }

        $updateUser->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'dob' => $request->input('dob'),
            'gender' => $request->input('gender'),
            'contact_number' => $request->input('contact_number'),
            'profile' => $request->input('profile'),
            'updated_by' => $updated_by_data ?? null,
        ]);
        $userAcademics->update([
            'college_id' => $request->input('college_id'),
        ]);

        return response()->json([
            'message' => 'Data updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $collegeAdminDelete = User::where('id', $id)->where('status', 1)->first();
        $collegeAdminAcademics = UserAcademics::where('user_id', $collegeAdminDelete->id)->where('status', 1)->first();

        if (!$collegeAdminDelete) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ]);
        }
        $authId = Auth::id();
        if ($collegeAdminDelete->updated_by != null) {
            $updated_by_data = json_decode($collegeAdminDelete['updated_by'], true);
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

        $collegeAdminDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);
        $collegeAdminAcademics->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Data deleted successfully!',
            'status' => 200
        ]);
    }
}
