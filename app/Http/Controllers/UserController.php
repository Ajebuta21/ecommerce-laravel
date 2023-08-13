<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $user = User::all();

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user, 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string|min:1',
        ]);

        $keyword = '%' . $validatedData['keyword'] . '%';
        $users = User::where('username', 'like', $keyword)
            ->orWhere('email', 'like', $keyword)
            ->get();

        return response()->json($users, 200);
    }

    public function changeRole(Request $request, $id)
    {
        $validatedData = $request->validate([
            'role' => 'string|in:user,admin',
        ]);

        $user = User::findOrFail($id);
        $user->role = $validatedData['role'];
        $user->save();

        return response()->json(['message' => 'Role changed', $user], 201);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'age' => 'required|date',
            'phone' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        // Create the user
        $user = new User();
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->age = $request->input('age');
        $user->phone = $request->input('phone');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function editUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $id,
            'age' => 'required|date',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->username = $request->input('username');
        $user->age = $request->input('age');
        $user->phone = $request->input('phone');
        $user->save();

        return response()->json(['message' => 'Account updated successfully'], 200);
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $user->address = $request->input('address');
        $user->save();

        return response()->json(['message' => 'Address updated successfully'], 200);
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        if (!password_verify($request->input('old_password'), $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 422);
        }

        $user->password = bcrypt($request->input('new_password'));
        $user->save();

        return response()->json(['message' => 'Password reset successful'], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function countUsers()
    {
        $totalUsers = User::count();

        $last30Days = Carbon::now()->subDays(30);
        $newUsersLast30Days = User::where('created_at', '>=', $last30Days)->count();

        return response()->json([
            'total_users' => $totalUsers,
            'new_users_last_30_days' => $newUsersLast30Days
        ], 200);
    }
}
