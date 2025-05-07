<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyTag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyTagController extends Controller
{
    //
    public function index()
    {
        $companyTags = CompanyTag::where('status', 1)->select('id', 'tag_name', 'status', 'created_by', 'updated_by')->get()->toArray();

        if ($companyTags == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'tags fetched successfully!',
            'data' => $companyTags,
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
                Rule::unique('company_tags', 'tag_name')
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

       
        $createdCompanyTags = CompanyTag::create([
            'tag_name' => $request->input('tag_name'),
            'created_by' => null,
            'status' => 1
        ]);
        if (!$createdCompanyTags) {
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
        $CompanyTag = CompanyTag::where('status', 1)->select('id')->where('id', $id)->first();
        if (!$CompanyTag) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ], 404);
        }
        $CompanyTag = json_decode(json_encode($CompanyTag), true);
        return response()->json([
            'message' => 'Tag fetched successfully!',
            'data' => $CompanyTag,
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
                Rule::unique('company_tags', 'tag_name')
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

        $updateCompanyTag = CompanyTag::where('status', 1)->select('id')->where('id', $id)->first();

        if (!$updateCompanyTag) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ], 404);
        }
    
        // Perform update
        $updateCompanyTag->update([
            'tag_name' => $request->input('tag_name'),
            'updated_by' => 1,
        ]);
    
        // Return success response
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
        $CompanytagDelete = CompanyTag::where('id', $id)->select('id', 'status')->where('status', 1)->first();

        if (!$CompanytagDelete) {
            return response()->json([
                'message' => 'Tag not found!',
                'status' => 404
            ]);
        }
        $CompanytagDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Tag deleted successfully!',
            'status' => 200
        ]);
    }
}
