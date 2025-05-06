<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batch = Batch::where('status', 1)->select('id', 'batch_name', 'created_by', 'updated_by')->get()->toArray();
        if ($batch == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Batch data fetched successfully!',
            'data' => $batch,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        $hasData = Batch::where('status', 1)->where('batch_name', $request->input('batch_name'))->exists();
        if ($hasData) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'batch_name' => ['The batch name has already been taken.']
                ]
            ], 422);
        }

        $createdBatch = Batch::create([
            'batch_name' => $request->input('batch_name'),
            'created_by' => null,
            'status' => 1
        ]);
        if (!$createdBatch) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ], 500);
        }
        return response()->json([
            'message' => 'Batch created successfully!!',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batch = Batch::where('status', 1)->where('id', $id)->select('id', 'batch_name', 'created_by', 'updated_by')->first();
        if (!$batch) {
            return response()->json([
                'message' => 'Data not found!',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'message' => 'Batch data fetched successfully!',
            'data' => $batch,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateBatch = Batch::where('status', 1)->where('id', $id)->select('id', 'batch_name', 'created_by', 'updated_by')->first();

        if (!$updateBatch) {
            return response()->json([
                'message' => 'Department not found!',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'batch_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($updateBatch->batch_name !== $request->input('batch_name')) {
            $hasData = Batch::where('status', 1)->where('batch_name', $request->input('batch_name'))->exists();
            if ($hasData) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        'batch_name' => ['The batch name has already been taken.']
                    ]
                ], 422);
            }
        }

        $updateBatch->update([
            'batch_name' => $request->input('batch_name', $updateBatch->batch_name),
            'updated_by' => 1,
        ]);

        return response()->json([
            'message' => 'Batch updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $batchDelete = Batch::where('id', $id)->where('status', 1)->first();

        if (!$batchDelete) {
            return response()->json([
                'message' => 'Batch not found!',
                'status' => 404
            ], 404);
        }
        $batchDelete->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'Batch deleted successfully!',
            'status' => 200
        ]);
    }
}
