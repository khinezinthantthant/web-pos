<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
            ]);
        }
        $roles = User::where('role', 'ban')->get("role");
        // $users = User::latest("id")->paginate(5)->withQueryString();
        if ($roles) {
            return User::where("role", "admin")->orWhere("role", "staff")->get();
        }
    }
    public function banUser(Request $request, $id)
    {

        // Gate::authorize('admin');
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
            ]);
        }
        $request->validate([
            "name" => "nullable|min:3",
            "email" => "email|unique:users",
            "password" => "min:8",
            "phone_number" => "min:9|max:15",
            "address" => "min:5",
            "gender" => "in:male,female",
            "date_of_birth" => "date",
            "role" => "in:admin,staff,ban",
            "photo" => "nullable"
        ]);
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $user = User::find($id);
        $user->role = "ban";
        $user->update();

        return response()->json([
            "message" => "User banned successfully",
            "user" => $user

        ]);
    }
    public function banUserList()
    {
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
            ]);
        }
        // Gate::authorize("admin");
        $users = User::where("role", "ban")->latest("id")->paginate(10)->withQueryString();
        return response()->json([
            "users" => $users
        ]);
    }
    public function restore(Request $request, $id)
    {
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
            ]);
        }
        $request->validate([
            "name" => "nullable|min:3",
            "email" => "email|unique:users",
            "password" => "min:8",
            "phone_number" => "min:9|max:15",
            "address" => "min:5",
            "gender" => "in:male,female",
            "date_of_birth" => "date",
            "role" => "in:admin,staff,ban",
            "photo" => "nullable"
        ]);
        // $user = User::withTrashed()->findOrFail($id);
        // if ($user->trashed()) {
        //     $user->restore();
        //     return response()->json(['message' => 'User restored successfully']);
        // }else{
        //     return response()->json(['message' => 'User is not soft-delete'], 404);
        // }

        $user = User::withTrashed()->find($id);
        if (is_null($user)) {
            return response()->json([
                "message" => "Softdeleted user is not found"
            ]);
        }

        if ($user->restore()) {
            return response()->json([
                "message" => "User has been restored",
                "user" => $user
            ]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Gate::authorize("admin");
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
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
            "role" => "required|in:admin,staff",
            "photo" => "required"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone_number" => $request->phone_number,
            "address" => $request->address,
            "gender" => $request->gender,
            "date_of_birth" => $request->date_of_birth,
            "role" => 'staff',
            "photo" => $request->photo ?? config("info.default_user_photo")
        ]);

        // retur n $user;
        return response()->json([
            "message" => "Successfully created an account",
            "user" => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
            ]);
        }
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
        // Gate::authorize("admin");
        if (Gate::denies("admin")) {
            return response()->json([
                "message" => "This action is unauthorized"
            ]);
        }
        $request->validate([
            "name" => "nullable|min:3",
            "email" => "required|email|unique:users,email,$id",
            "phone_number" => "required|min:9|max:15",
            "address" => "required|min:5",
            "gender" => "required|in:male,female",
            "date_of_birth" => "required",
            "photo" => "nullable",
            "role" => "required|in:admin,staff,ban"
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
            "role" => $request->role,
            "photo" => $request->photo ?? config("info.default_user_photo")
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
        $request->validate([
            "current_password" => "required|min:8",
            "password" => "required|confirmed|min:8|max:15",
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


    public function modifyPassword(Request $request)
    {
        Gate::authorize("admin");

        $request->validate([
            "user_id" => "required|exists:users,id",
            "new_password" => "required|min:8|max:15"
        ]);
        $user = User::find($request->user_id);
        if (is_null($user)) {
            return response()->json([
                'message' => 'User not found'
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        //clear token
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Staff password changed successfully'
        ]);
    }


    // public function ban(string $id)
    // {
    //     Gate::authorize('admin');
    //     $user = User::find($id);
    //     if (is_null($user)) {
    //         return response()->json([
    //             'message' => 'user not found'
    //         ], 404);
    //     }

    //     $user->update([
    //         'role' => 'ban'
    //     ]);

    //     return response()->json([
    //         'message' => 'User has been banned'
    //     ]);
    //     //
    // }

    // public function bannedUsers()
    // {
    //     Gate::authorize("admin");
    //     // banned users
    //     $banneUsers = collect(User::all())->where('role', 'ban');
    //     return $banneUsers;
    // }

    // public function restoreUser(string $id)
    // {
    //     Gate::authorize('admin');

    //     $user = User::find($id);
    //     if (is_null($user)) {
    //         return response()->json([
    //             'message' => 'user not found'
    //         ], 404);
    //     }

    //     $user->update([
    //         'role' => 'staff'
    //     ]);

    //     return response()->json([
    //         'message' => 'User has been restored'
    //     ]);
    //     //
    // }


    public function bannedUsers()
    {
        $user = User::onlyTrashed()
            ->where("id", auth()->id())
            ->get();

        return response()->json([
            $user
        ]);
    }

    public function restoreUser($id)
    {
        return $id;
        Gate::authorize("admin");

        $user = User::onlyTrashed()
            ->where("id", auth()->id())
            ->findOrFail($id);

        $user->restore();

        return response()->json([
            "message" => "User has been restored"
        ]);
    }
}
