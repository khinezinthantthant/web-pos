<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest("id")->paginate(5)->withQueryString();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('admin')) {
            return response()->json([
                'message' => "You are not allow"
            ]);
        }

        $request->validate([
            "name" => "nullable|min:3",
            "email" => "required|email|unique:users",
            "password" => "required|min:8",
            "phone_number" => "required|min:9|max:15",
            "address" => "required|min:5",
            "gender" => "required|in:male,female",
            "date_of_birth" => "required",
            // "role" => "required|in:admin,staff"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone_number" => $request->phone_number,
            "address" => $request->address,
            "gender" => $request->gender,
            "date_of_birth" => $request->date_of_birth,
            "role" => 'staff'
        ]);

        // return $user;
        return response()->json([
            "message" => "User created successful",
            $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                // "success" => false,
                "message" => "User not found",

            ], 404);
        }

        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            "name" => "nullable|min:3",
            "email" => "required|email|unique:users,email,$id",
            "phone_number" => "required|min:9|max:15",
            "address" => "required|min:5",
            "gender" => "required|in:male,female",
            "date_of_birth" => "required",
            // "role" => "required|in:admin,staff"
        ]);

        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "phone_number" => $request->phone_number,
            "address" => $request->address,
            "gender" => $request->gender,
            "date_of_birth" => $request->date_of_birth,
            "role" => 'staff'
        ]);

        // return $user;
        return response()->json([
            "message" => "User Updated successful",
            $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Gate::allows('admin')) {
            return response()->json([
                'message' => "You are not allow"
            ]);
        }
        $user = User::find($id);
        $user->delete();
        return response()->json([
            "message" => "User Successfully deleted"
        ]);
    }

    public function passwordChanging(Request $request)
    {
        // return Auth::user();
        // $request->validate([
        //     "current_password" => "required|min:8",
        //     "password" => "required|confirmed",
        // ]);

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


    public function modifyPassword(Request $request)
    {
        if (!Gate::allows('admin')) {
            return response()->json([
                'message' => "You are not allow"
            ]);
        }
        $request->validate([
            "new_password" => "required|min:8|max:15",
            "user_id" => "required|exists:users,id"
        ]);
        $user = User::find($request->user_id);
        if (is_null($user)) {
            return response()->json([
                'message' => 'User not found'
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'message' => 'Staff password changed successfully'
        ]);
    }
}
