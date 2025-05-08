<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CollegeController extends Controller
{
    // get the Colleges
    public function index()
    {
        $colleges = College::where('status', 1)->select('id', 'college_name', 'college_short_name', 'college_image', 'created_by', 'updated_by')->get()->toArray();
        if ($colleges == []) {
            return response()->json([
                'message' => 'No Data found!',
                'data' => [],
                'status' => 404
            ], 200);
        }
        return response()->json([
            'message' => 'fetched successfully!',
            'data' => $colleges,
            'status' => 200
        ]);
    }

    // To store College
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'college_name' => 'required|max:255',
            'college_short_name' => [
                'required',
                'max:50',
                Rule::unique('colleges', 'college_short_name')->where(function ($query) {
                    return $query->where('status', 1);
                }),
            ],
            'college_image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdCollege = College::create([
            'college_name' => $request->input('college_name'),
            'college_short_name' => $request->input('college_short_name'),
            'college_image' => $request->input('college_image'),
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        if (!$createdCollege) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'College created successfully!!',
            'status' => 201
        ]);
    }

    // To view the college
    public function show(string $id)
    {
        $college = College::where('status', 1)->select('id', 'college_name', 'college_short_name', 'college_image', 'created_by', 'updated_by')->where('id', $id)->first();

        if (!$college) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $college = json_decode(json_encode($college), true);
        return response()->json([
            'message' => 'College data fetched successfully!',
            'data' => $college,
            'status' => 200
        ], 200);
    }

    // To update the college
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'college_name' => 'required|max:255',
            'college_short_name' => [
                'required',
                'max:50',
                Rule::unique('colleges', 'college_short_name')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
            'college_image' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $authId = Auth::id();

        $updateCollege = College::where('status', 1)->select('id','updated_by')->where('id', $id)->first();
        // dd($updateCollege->updated_by,$updateCollege);
        if ($updateCollege->updated_by != null) {
            $updated_by_data = json_decode($updateCollege['updated_by'], true);
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

        if (!$updateCollege) {
            return response()->json([
                'message' => 'College not found!',
                'status' => 404
            ], 404);
        }

        $updateCollege->update([
            'college_name' => $request->input('college_name'),
            'college_short_name' => $request->input('college_short_name'),
            'college_image' => $request->input('college_image'),
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'College updated successfully!',
            'status' => 200
        ], 200);
    }


    //    To delete the college
    public function destroy(string $id)
    {
        $clgDelete = College::where('id', $id)->where('status', 1)->first();

        if (!$clgDelete) {
            return response()->json([
                'message' => 'College not found!',
                'status' => 404
            ]);
        }
        $clgDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'College deleted successfully!',
            'status' => 200
        ]);
    }
}
