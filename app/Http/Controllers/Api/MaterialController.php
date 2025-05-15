<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaterialBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = MaterialBank::where('status', 1)->select('id', 'course_id', 'topic_id', 'path', 'file_type', 'qb_type', 'created_by', 'updated_by')->get()->toArray();

        return response()->json([
            'message' => 'fetched successfully!',
            'data' => $materials,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'topic_id' => 'required',
            'path' => 'required',
            'file_type' => 'required',
            'qb_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdMaterial = MaterialBank::create([
            'course_id' => $request->input('course_id'),
            'topic_id' => $request->input('topic_id'),
            'path' => $request->input('path'),
            'file_type' => $request->input('file_type'),
            'qb_type' => $request->input('qb_type'),
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        if (!$createdMaterial) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'Data created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $material = MaterialBank::where('status', 1)->select('id', 'course_id', 'topic_id', 'path', 'file_type', 'qb_type', 'created_by', 'updated_by')->where('id', $id)->first();

        if (!$material) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404,
            ], 404);
        }
        $material = json_decode(json_encode($material), true);
        return response()->json([
            'message' => 'Data fetched successfully!',
            'data' => $material,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'topic_id' => 'required',
            'path' => 'required',
            'file_type' => 'required',
            'qb_type' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $authId = Auth::id();

        $updateMaterial = MaterialBank::where('status', 1)->select('id', 'updated_by')->where('id', $id)->first();
        // dd($updateCollege->updated_by,$updateCollege);
        if ($updateMaterial->updated_by != null) {
            $updated_by_data = json_decode($updateMaterial['updated_by'], true);
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

        if (!$updateMaterial) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }

        $updateMaterial->update([
            'course_id' => $request->input('course_id'),
            'topic_id' => $request->input('topic_id'),
            'path' => $request->input('path'),
            'file_type' => $request->input('file_type'),
            'qb_type' => $request->input('qb_type'),
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
        $materialDelete = MaterialBank::where('id', $id)->where('status', 1)->first();

        if (!$materialDelete) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ]);
        }
        $authId = Auth::id();
        if ($materialDelete->updated_by != null) {
            $updated_by_data = json_decode($materialDelete['updated_by'], true);
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

        $materialDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Data deleted successfully!',
            'status' => 200
        ]);
    }
}