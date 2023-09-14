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

    public function login(Request $request)
    {

        $request->validate([
            "email" => "required|email|exists:users,email",
            "password" => "required|min:8"
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                "message" => "Username or password wrong",
            ]);
        }

        return response()->json([
            "message" => "login successful",
            "token" => Auth::user()->createToken($request->has("device") ? $request->device : 'unknown')->plainTextToken,
            "user" => Auth::user()
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            "message" => "logout successful"
        ]);
    }

    public function logoutAll()
    {
        foreach (Auth::user()->tokens as $token) {
            $token->delete();
        }
        return response()->json([
            "message" => "logout all devices successful"
        ]);
    }

    public function passwordChanging(Request $request)
    {
        //    return Auth::user();
        $request->validate([
            "current_password" => "required|min:8",
            "password" => "required|confirmed",
        ]);


        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json(["current_password" => "Password does not match"]);
        }

        $id = Auth::id();
        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->update();

        //clear token
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "password change successful.",
        ]);
    }
}
