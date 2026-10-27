<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManagedUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManagedUserController extends Controller
{
    public function index(Request $request)
    {
        $users = ManagedUser::where(
            'owner_user_id',
            $request->user()->id
        )->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $ownerId = $request->user()->id;

        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in([
                    'Partner',
                    'Salesman',
                    'Stock Manager',
                    'Delivery Boy',
                    'CA',
                ]),
            ],
            'name' => 'required|string|max:255',
            'phone' => [
    'required',
    'regex:/^[0-9]{10}$/',
    Rule::unique('managed_users', 'phone'),
],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('managed_users', 'email')
                    ->where('owner_user_id', $ownerId),
            ],
        ]);

        $managedUser = ManagedUser::create([
            'owner_user_id' => $ownerId,
            'role' => $validated['role'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User added successfully.',
            'data' => $managedUser,
        ], 201);
    }
}