<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuestionTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionTagController extends Controller
{
    //

    public function index()
    {
        $tags = QuestionTag::where('status', 1)->select('id', 'tag_name', 'status', 'created_by', 'updated_by')->get()->toArray();
        if ($tags == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 200
            ], 200);
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
            'tag_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('question_tags', 'tag_name')
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


        $createdTag = QuestionTag::create([
            'tag_name' => $request->input('tag_name'),
            'created_by' => Auth::id() ?? null,
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
        $tag = QuestionTag::where('status', 1)->select('id')->where('id', $id)->first();
        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ], 404);
        }
        $tag = json_decode(json_encode($tag), true);
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

        $validator = Validator::make($request->all(), [
            'tag_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('question_tags', 'tag_name')
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

        $updateTag = QuestionTag::where('status', 1)->select('id','updated_by')->where('id', $id)->first();

        $authId = Auth::id();
        if ($updateTag['updated_by'] != null) {
            $updated_by_data = json_decode($updateTag['updated_by'], true);
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


        if (!$updateTag) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ], 404);
        }

        $updateTag->update([
            'tag_name' => $request->input('tag_name'),
            'updated_by' => $updated_by_data ?? null,
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
        $tagDelete = QuestionTag::where('id', $id)->select('id', 'status')->where('status', 1)->first();

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
