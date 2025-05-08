<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compilers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompilerController extends Controller
{
    //
    public function index()
    {
        $compilers = Compilers::where('status', 1)->select('api', 'count', 'status', 'created_by', 'updated_by')->get()->toArray();
        
        return response()->json([
            'message' => 'compilers fetched successfully!',
            'data' => $compilers,
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

       
        $createdCompiler = Compilers::create([
            'api' => $request->input('api'),
            'count' => 0,
            'created_by' => Auth::id() ?? null,
            'status' => 1
        ]);
        if (!$createdCompiler) {
            return response()->json([
                'message' => 'Something went wrong!!',
                'status' => 500
            ]);
        }
        return response()->json([
            'message' => 'Compiler created successfully!!',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Compiler = Compilers::where('status', 1)->where('id', $id)->first();
        if (!$Compiler) {
            return response()->json([
                'message' => 'Compiler not found!',
                'status' => 404
            ], 404);
        }
        $Compiler = json_decode(json_encode($Compiler), true);
        return response()->json([
            'message' => 'Compiler fetched successfully!',
            'data' => $Compiler,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'api' => 'required|string|max:255' . $id,
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateCompiler = Compilers::where('status', 1)->select('id')->where('id', $id)->first();
    
        if (!$updateCompiler) {
            return response()->json([
                'message' => 'Compiler not found!',
                'status' => 404
            ], 404);
        }

        $authId = Auth::id();
        if ($updateCompiler['updated_by'] != null) {
            $updated_by_data = json_decode($updateCompiler['updated_by'], true);
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
    
        // Perform update
        $updateCompiler->update([
            'api' => $request->input('api'),
            'updated_by' => 1,
        ]);
    
        // Return success response
        return response()->json([
            'message' => 'Compiler updated successfully!',
            'status' => 200
        ], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $CompilerDelete = Compilers::where('id', $id)->select('id', 'status')->where('status', 1)->first();

        if (!$CompilerDelete) {
            return response()->json([
                'message' => 'Compiler not found!',
                'status' => 404
            ]);
        }
        $authId = Auth::id();
        if ($CompilerDelete['updated_by'] != null) {
            $updated_by_data = json_decode($CompilerDelete['updated_by'], true);
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
        $CompilerDelete->update([
            'status' => 0,
            'updated_by' => $updated_by_data ?? null,
        ]);

        return response()->json([
            'message' => 'Compiler deleted successfully!',
            'status' => 200
        ]);
    }
}
