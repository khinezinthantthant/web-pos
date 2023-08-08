<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        if (!Gate::allows('admin')) {
            return response()->json([
                'message' => "You are not allow"
            ]);
        }
        $request->validate([
            "name" => "nullable|min:3",
            "email" => "required|email|unique:users",
            "password" => "required|min:8"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "message" => "User register successful",
        ]);
    }

    public function login(Request $request)
    {
        // return Auth::user()->password;
        $request->validate([
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                "message" => "Username or password wrong",
            ]);
        }

        return Auth::user()->createToken($request->has("device") ? $request->device : 'unknown');
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            "message" => "logout successful"
        ]);
    }
}
