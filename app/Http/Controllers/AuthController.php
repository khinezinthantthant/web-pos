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
        // return response()->json([
        //     "message" => "password is correct." 
        // ]);
        $id = Auth::id();
        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->update();
        // return $user;
        // return $user->password;

        //clear token
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "password change successful.",
        ]);

        // update new password
        // $user = User::find(Auth::user()->id);
        // $user->password = Hash::make($request->password);
        // $user->update();

        // return $user->password;

        // clear auth session
        // session()->forget("auth");

        // return redirect()->route("auth.login");

    }
}
