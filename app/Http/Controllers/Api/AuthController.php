<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Please enter all the fields', 'errors' => $validator->errors(), 'status' => 422], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'The email does not exist', 'status' => 422,'errors' =>['email'=>['The email does not exist']]], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The password does not match with the provided email.', 'status' => 422,'errors' =>['password'=>['The password does not match with the provided email.']]], 422);
        }
        // $token = $user->createToken('Auth-token')->plainTextToken;
        // $user->tokens()->where('name', 'Auth-token')->latest()->first()->update([
        //     'expires_at' => now()->addHours(1)
        // ]);

        $tokenResult = $user->createToken('Auth-token');
        $token = $tokenResult->plainTextToken;
        $tokenResult->accessToken->expires_at = now()->addHours(2); // Or any duration
        $tokenResult->accessToken->save();

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'status' => 200,
            'role'=> $user['role']
        ], 200);
    }
}
