<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // $user->tokens()->delete();
            $token = $user->createToken('api-token')->plainTextToken;
            $kantors = Kantor::orderBy('name')->get();

            return response()->json([
                'message' => 'Login success',
                'user' => $user,
                'token' => $token,
                'kantors' => $kantors
            ]);
        }

        return response()->json([
            'message' => 'Login failed'
        ], 401);
    }

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
