<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $section = Section::where('status', 1)->select('id', 'section_name', 'created_by', 'updated_by')->get()->toArray();
        if ($section == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Section data fetched successfully!',
            'data' => $section,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $hasData = Section::where('status', 1)->where('section_name', $request->input('section_name'))->exists();
        if ($hasData) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'section_name' => ['The section name has already been taken.']
                ]
            ], 422);
        }

        $createdSection = Section::create([
            'section_name' => $request->input('section_name'),
            'created_by' => null,
            'status' => 1
        ]);
        if (!$createdSection) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ], 500);
        }
        return response()->json([
            'message' => 'Section created successfully!!',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $section = Section::where('status', 1)->where('id', $id)->select('id', 'section_name', 'created_by', 'updated_by')->first();
        if (!$section) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Section data fetched successfully!',
            'data' => $section,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateSection = Section::where('status', 1)->where('id', $id)->select('id', 'section_name', 'created_by', 'updated_by')->first();

        if (!$updateSection) {
            return response()->json([
                'message' => 'Section not found!',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'section_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateSection->update([
            'section_name' => $request->input('section_name',$updateSection->section_name),
            'updated_by' => 1,
        ]);

        return response()->json([
            'message' => 'Section updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sectionDelete = Section::where('id', $id)->where('status', 1)->first();

        if (!$sectionDelete) {
            return response()->json([
                'message' => 'Section not found!',
                'status' => 404
            ], 404);
        }
        $sectionDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Section deleted successfully!',
            'status' => 200
        ]);
    }
}
