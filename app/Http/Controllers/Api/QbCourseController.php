<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QbCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QbCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = QbCourse::where('status', 1)->select('id', 'course_name', 'created_by', 'updated_by')->get()->toArray();
       
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
            'created_by' => Auth::id() ?? null,
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
                'status' => 200,
                'data' => [],
            ], 200);
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

        $updateCourse = QbCourse::where('status', 1)->select('id','updated_by')->where('id', $id)->first();

        if (!$updateCourse) {
            return response()->json([
                'message' => 'Course not found!',
                'status' => 404
            ], 404);
        }

        $authId = Auth::id();
        if ($updateCourse['updated_by'] != null) {
            $updated_by_data = json_decode($updateCourse['updated_by'], true);
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

        $updateCourse->update([
            'course_name' => $request->input('course_name'),
            'updated_by' => $updated_by_data ?? null,
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
        $authId = Auth::id();
        if ($courseDelete->updated_by != null) {
            $updated_by_data = json_decode($courseDelete['updated_by'], true);
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
        $courseDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Course deleted successfully!',
            'status' => 200
        ]);
    }
}
