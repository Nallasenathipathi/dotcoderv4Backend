<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAcademics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $staffs = User::where('status', 1)->where('role', 6)->select('id', 'name', 'email', 'password', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')->get();
        // $staffs = User::where('status', 1)
        //     ->where('role', 6)
        //     ->with(['academic.college:id,college_name'])
        //     ->select('id', 'name', 'email', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')
        //     ->get();
        // $staffs = $staffs->map(function ($staff) {
        //     $staff->college_name = $staff->academic?->college?->college_name ?? null;
        //     unset($staff->academic);
        //     return $staff;
        // });

        // return response()->json([
        //     'message' => 'Data fetched successfully!',
        //     'data' => $staffs,
        //     'status' => 200
        // ]);

        $staffs = DB::table('users')
            ->leftJoin('user_academics', 'users.id', '=', 'user_academics.user_id')
            ->leftJoin('colleges', 'user_academics.college_id', '=', 'colleges.id')
            ->where('users.status', 1)
            ->where('users.role', 6)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.gender',
                'users.dob',
                'users.role',
                'users.contact_number',
                'users.profile',
                'users.created_by',
                'users.updated_by',
                'colleges.college_name as college_name'
            )
            ->get();

        // $staffs = $staffs->map(function ($staff) {
        //     $academic = UserAcademics::where('user_id', $staff->id)
        //         ->with('college:id,college_name') // eager load college name
        //         ->first();

        //     $staff->college_name = $academic?->college?->college_name ?? null;

        //     return $staff;
        // });

        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $staffs,
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
            'role' => 6,
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
        // $staff = User::where('status', 1)->select('id', 'name', 'email', 'password', 'gender', 'dob', 'role', 'contact_number', 'profile', 'created_by', 'updated_by')->where('id', $id)->first();
        $staff = DB::table('users')
            ->leftJoin('user_academics', 'users.id', '=', 'user_academics.user_id')
            ->leftJoin('colleges', 'user_academics.college_id', '=', 'colleges.id')
            ->where('users.status', 1)
            ->where('users.id', $id)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.password',
                'users.gender',
                'users.dob',
                'users.role',
                'users.contact_number',
                'users.profile',
                'users.created_by',
                'users.updated_by',
                'colleges.college_name'
            )
            ->first();

        if (!$staff) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $staff = json_decode(json_encode($staff), true);
        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $staff,
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
        if ($userAcademics) {
            $userAcademics->update([
                'college_id' => $request->input('college_id'),
            ]);
        }

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
        $staffDelete = User::where('id', $id)->where('status', 1)->first();
        $staffAcademics = UserAcademics::where('user_id', $staffDelete->id)->where('status', 1)->first();

        if (!$staffDelete) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ]);
        }
        $authId = Auth::id();
        if ($staffDelete->updated_by != null) {
            $updated_by_data = json_decode($staffDelete['updated_by'], true);
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

        $staffDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);
        $staffAcademics->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Data deleted successfully!',
            'status' => 200
        ]);
    }
}
