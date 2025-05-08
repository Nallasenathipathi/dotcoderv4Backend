<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    /**
     * Display a lists of batch.
     */
    public function index()
    {
        $batch = Batch::where('status', 1)->select('id', 'batch_name', 'created_by', 'updated_by')->get()->toArray();
        if ($batch == []) {
            return response()->json([
                'message' => 'No Data found!',
                'status' => 200,
                'data' => []
            ], 200);
        }
        return response()->json([
            'message' => 'Batch data fetched successfully!',
            'data' => $batch,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_name' => [
                'required',
                Rule::unique('batches', 'batch_name')->where(function ($query) {
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

        $createdBatch = Batch::create([
            'batch_name' => $request->input('batch_name'),
            'created_by' => Auth::id() ?? null,
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
     * Display the specified batch.
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
        $batch = json_decode(json_encode($batch), true);
        return response()->json([
            'message' => 'Batch data fetched successfully!',
            'data' => $batch,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $validator = Validator::make($request->all(), [
            Rule::unique('batches', 'batch_name')->ignore($id)->where(function ($query) {
                return $query->where('status', 1);
            }),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $updateBatch = Batch::where('status', 1)->where('id', $id)->select('id', 'batch_name', 'created_by', 'updated_by')->first();

        if (!$updateBatch) {
            return response()->json([
                'message' => 'Department not found!',
                'status' => 404
            ], 404);
        }
        $authId = Auth::id();
        if ($updateBatch['updated_by'] != null) {
            $updated_by_data = json_decode($updateBatch['updated_by'], true);
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

        $updateBatch->update([
            'batch_name' => $request->input('batch_name'),
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Batch updated successfully!',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified batch from storage.
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
        $authId = Auth::id();
        if ($batchDelete->updated_by != null) {
            $updated_by_data = json_decode($batchDelete['updated_by'], true);
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
        $batchDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Batch deleted successfully!',
            'status' => 200
        ]);
    }
}
