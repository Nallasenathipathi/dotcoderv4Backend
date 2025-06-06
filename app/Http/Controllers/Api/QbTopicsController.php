<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QbTopics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QbTopicsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = QbTopics::with('course:id,course_name')->where('status', 1)->select('id', 'course_id', 'topic_tag_id', 'topic_name', 'created_by', 'updated_by')->get()->toArray();

        return response()->json([
            'message' => 'fetched successfully!',
            'data' => $topics,
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
            'topic_tag_id' => 'required',
            'topic_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdCourse = QbTopics::create([
            'course_id' => $request->input('course_id'),
            'topic_tag_id' => $request->input('topic_tag_id'),
            'topic_name' => $request->input('topic_name'),
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
            'message' => 'Qb Topics created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $topics = QbTopics::with('course:id,course_name')->where('status', 1)->select('id', 'course_id', 'topic_tag_id', 'topic_name', 'created_by', 'updated_by')->where('id', $id)->first();

        if (!$topics) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        $topics = json_decode(json_encode($topics), true);
        return response()->json([
            'message' => 'Qb topics fetched successfully!',
            'data' => $topics,
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
            'topic_tag_id' => 'required',
            'topic_name' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateTopics = QbTopics::where('status', 1)->select('id', 'updated_by')->where('id', $id)->first();
        $authId = Auth::id();
        if ($updateTopics['updated_by'] != null) {
            $updated_by_data = json_decode($updateTopics['updated_by'], true);
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

        if (!$updateTopics) {
            return response()->json([
                'message' => 'Topics not found!',
                'status' => 404
            ], 404);
        }

        $updateTopics->update([
            'course_id' => $request->input('course_name'),
            'topic_tag_id' => $request->input('topic_tag_id'),
            'topic_name' => $request->input('topic_name'),
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Qb Topics updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $topicsDelete = QbTopics::where('id', $id)->where('status', 1)->first();

        if (!$topicsDelete) {
            return response()->json([
                'message' => 'Topics not found!',
                'status' => 404
            ]);
        }
        $authId = Auth::id();
        if ($topicsDelete['updated_by'] != null) {
            $updated_by_data = json_decode($topicsDelete['updated_by'], true);
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
        $topicsDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Topic deleted successfully!',
            'status' => 200
        ]);
    }
}
