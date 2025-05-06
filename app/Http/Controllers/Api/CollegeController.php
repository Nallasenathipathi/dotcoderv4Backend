<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollegeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colleges = College::where('status', 1)->get()->toArray();
        if ($colleges == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'College data fetched successfully!',
            'data' => $colleges,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'college_name' => 'required|max:255',
            'college_short_name' => 'required|max:50|unique:colleges,college_short_name',
            'college_image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // if ($request->hasFile('college_image')) {

        //     $extension = $request->file('college_image')->extension();

        //     $clg_imageName = time() . '.' .  $extension;

        //     $college_image_path = $request->file('college_image')->storeAs('CollegeImages', $clg_imageName, 'public');
        // }
        $createdCollege = College::create([
            'college_name' => $request->input('college_name'),
            'college_short_name' => $request->input('college_short_name'),
            'college_image' => $request->input('college_image'),
            'created_by' => null,
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $college = College::where('status', 1)->where('id', $id)->first();
        if (!$college) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'College data fetched successfully!',
            'data' => $college,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateCollege = College::where('status', 1)->where('id', $id)->first();
    
        if (!$updateCollege) {
            return response()->json([
                'message' => 'College not found!',
                'status' => 404
            ], 404);
        }
    
        $updateCollege->update([
            'college_name' => $request->input('college_name', $updateCollege->college_name),
            'college_short_name' => $request->input('college_short_name', $updateCollege->college_short_name),
            'college_image' => $request->input('college_image', $updateCollege->college_image),
            'updated_by' => 1,
        ]);
    
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
