<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QbCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QbCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = QbCourse::where('status', 1)->select('id', 'course_name', 'created_by', 'updated_by')->get()->toArray();
        if ($courses == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'fetched successfully!',
            'data' => $courses,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdCourse = QbCourse::create([
            'course_name' => $request->input('course_name'),
            'created_by' => auth()->id ?? null,
            'status' => 1
        ]);
        if (!$createdCourse) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'Qb Course created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = QbCourse::where('status', 1)->select('id', 'course_name', 'created_by', 'updated_by')->where('id', $id)->first();

        if (!$course) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $course = json_decode(json_encode($course), true);
        return response()->json([
            'message' => 'Course data fetched successfully!',
            'data' => $course,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateCourse = QbCourse::where('status', 1)->select('id')->where('id', $id)->first();

        if (!$updateCourse) {
            return response()->json([
                'message' => 'Course not found!',
                'status' => 404
            ], 404);
        }

        $updateCourse->update([
            'course_name' => $request->input('course_name'),
            'updated_by' => 1,
        ]);

        return response()->json([
            'message' => 'Qb Course updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $courseDelete = QbCourse::where('id', $id)->where('status', 1)->first();

        if (!$courseDelete) {
            return response()->json([
                'message' => 'Course not found!',
                'status' => 404
            ]);
        }
        $courseDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Course deleted successfully!',
            'status' => 200
        ]);
    }
}
