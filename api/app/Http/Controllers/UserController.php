<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function getUsersList(Request $request)
{
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        $users = \App\Models\User::skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $total = \App\Models\User::count();

        return response()->json([
            'users' => $users,
            'total' => $total,
        ]);
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
            'permission' => 'required',
        ]);

        Log::info("message", ['permission' => $validated['permission']]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'last_name' => $validated['surname'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'permission' => $validated['permission'],
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
}
