<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainers = User::where('status', 1)->where('role', 3)->select('id', 'name', 'email', 'password', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')->get()->toArray();

        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $trainers,
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
            'role' => 3,
            'contact_number' => $request->input('contact_number'),
            'profile' => $request->input('profile'),
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        if (!$createdTrainer) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ], 500);
        }
        return response()->json([
            'message' => 'Trainer created successfully!!',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trainer = User::where('status', 1)->select('id', 'name', 'email', 'password', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')->where('id', $id)->first();

        if (!$trainer) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $trainer = json_decode(json_encode($trainer), true);
        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $trainer,
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
        if ($updateUser->updated_by != null) {
            $updated_by_data = json_decode($updateUser['updated_by'], true);
            // dd($updated_by_data);
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
        $trainerDelete = User::where('id', $id)->where('status', 1)->first();

        if (!$trainerDelete) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ]);
        }
        $authId = Auth::id();
        if ($trainerDelete->updated_by != null) {
            $updated_by_data = json_decode($trainerDelete['updated_by'], true);
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

        $trainerDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Data deleted successfully!',
            'status' => 200
        ]);
    }
}
