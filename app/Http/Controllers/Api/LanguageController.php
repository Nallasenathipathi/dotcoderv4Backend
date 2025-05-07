<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Languages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    //
    public function index()
    {
        $Languages = Languages::where('status', 1)->select('lang_name', 'lang_id', 'lang_image', 'lang_category', 'status', 'created_by', 'updated_by')->get()->toArray();
        if ($Languages == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Languages fetched successfully!',
            'data' => $Languages,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang_name' => 'required|string|max:255',
            'lang_id' => [
                'required',
                Rule::unique('languages', 'lang_id')->where(function ($query) {
                    return $query->where('status', 1);
                }),
            ],
            'lang_image' => 'required',
            'lang_category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $createdLanugage = Languages::create([
            'lang_name' => $request->input('lang_name'),
            'lang_id' => $request->input('lang_id'),
            'lang_image' => $request->input('lang_image'),
            'lang_category' => $request->input('lang_category'),
            'created_by' => null,
            'status' => 1
        ]);

        if (!$createdLanugage) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'Language created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Lanugage = Languages::where('status', 1)->select('id')->where('id', $id)->first();
        if (!$Lanugage) {
            return response()->json([
                'message' => 'Lanugage not found!',
                'status' => 404
            ], 404);
        }
        $Lanugage = json_decode(json_encode($Lanugage), true);
        return response()->json([
            'message' => 'Language fetched successfully!',
            'data' => $Lanugage,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'lang_name' => 'required|string|max:255',
            'lang_id' => [
                'required',
                Rule::unique('languages', 'lang_id')
                    ->ignore($id)
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
            'lang_image' => 'required|string|max:255',
            'lang_category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateLanguage = Languages::where('status', 1)->select('id')->where('id', $id)->first();

        if (!$updateLanguage) {
            return response()->json([
                'message' => 'Language not found!',
                'status' => 404
            ], 404);
        }

        $updateLanguage->update([
            'lang_name' => $request->input('lang_name'),
            'lang_id' => $request->input('lang_id'),
            'lang_image' => $request->input('lang_image'),
            'lang_category' => $request->input('lang_category'),
            'updated_by' => 1,
        ]);

        return response()->json([
            'message' => 'Language updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $LanguageDelete = Languages::where('id', $id)->select('id', 'status')->where('status', 1)->first();

        if (!$LanguageDelete) {
            return response()->json([
                'message' => 'Language not found!',
                'status' => 404
            ]);
        }
        $LanguageDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Language deleted successfully!',
            'status' => 200
        ]);
    }
}
