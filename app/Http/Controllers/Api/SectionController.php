<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * Display a lists of section.
     */
    public function index()
    {
        $section = Section::where('status', 1)->select('id', 'section_name', 'created_by', 'updated_by')->get()->toArray();
        if ($section == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 200,
                'data' => []
            ], 200);
        }
        return response()->json([
            'message' => 'Section data fetched successfully!',
            'data' => $section,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created section in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_name' => [
                'required',
                Rule::unique('sections', 'section_name')
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

        $createdSection = Section::create([
            'section_name' => $request->input('section_name'),
            'created_by' => Auth::id() ?? null,
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
     * Display the specified section.
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
        $section = json_decode(json_encode($section), true);
        return response()->json([
            'message' => 'Section data fetched successfully!',
            'data' => $section,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified section in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'section_name' => [
                'required',
                Rule::unique('sections', 'section_name')
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
        $updateSection = Section::where('status', 1)->where('id', $id)->select('id', 'section_name', 'created_by', 'updated_by')->first();

        if (!$updateSection) {
            return response()->json([
                'message' => 'Section not found!',
                'status' => 404
            ], 404);
        }
        
        $authId = Auth::id();
        if ($updateSection['updated_by'] != null) {
            $updated_by_data = json_decode($updateSection['updated_by'], true);
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


        $updateSection->update([
            'section_name' => $request->input('section_name'),
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Section updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified section from storage.
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
        $authId = Auth::id();
        if ($sectionDelete->updated_by != null) {
            $updated_by_data = json_decode($sectionDelete['updated_by'], true);
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
        $sectionDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Section deleted successfully!',
            'status' => 200
        ]);
    }
}
