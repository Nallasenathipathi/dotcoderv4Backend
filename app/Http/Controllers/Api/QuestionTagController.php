<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuestionTag;
use Illuminate\Support\Facades\Validator;

class QuestionTagController extends Controller
{
    //

    public function index()
    {
        $tags = QuestionTag::where('status', 1)->get()->toArray();
        if ($tags == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Tags fetched successfully!',
            'data' => $tags,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|max:255|unique:question_tags,tag_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        $createdTag = QuestionTag::create([
            'tag_name' => $request->input('tag_name'),
            'created_by' => null,
            'status' => 1
        ]);
        if (!$createdTag) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'Tag created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tag = QuestionTag::where('status', 1)->where('id', $id)->first();
        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Tag fetched successfully!',
            'data' => $tag,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateTag = QuestionTag::where('status', 1)->where('id', $id)->first();

        if (!$updateTag) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|max:255' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $exists = QuestionTag::where('tag_name', $request->tag_name)
            ->where('id', '!=', $id)
            ->where('status', 1)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This tag name already exists for an active tag.',
                'status' => 422
            ], 422);
        }

        $updateTag->update([
            'tag_name' => $request->input('tag_name'),
            'updated_by' => 1,
        ]);

        return response()->json([
            'message' => 'Tag updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tagDelete = QuestionTag::where('id', $id)->where('status', 1)->first();

        if (!$tagDelete) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ]);
        }
        $tagDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Tag deleted successfully!',
            'status' => 200
        ]);
    }
}
