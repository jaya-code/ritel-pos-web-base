<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::forStore()->latest();
        
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role === 'owner') {
            // Owners can only create cashiers
            return view('users.create', ['roles' => ['kasir' => 'Cashier']]);
        }
        // Admins can create Owners and Cashiers (and other Admins)
        return view('users.create', ['roles' => ['admin' => 'Admin', 'owner' => 'Owner', 'kasir' => 'Cashier']]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,kasir,owner',
            'store_id' => 'nullable|exists:stores,id',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ];
        
        // If an admin creates a user, they can assign them to a store
        if (auth()->user()->role === 'admin') {
            $userData['store_id'] = $validated['store_id'] ?? null;
        } else {
            // Otherwise, inherit the creator's store (e.g. Owner creating Cashier)
            $userData['store_id'] = auth()->user()->store_id;
        }

        User::create($userData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (auth()->user()->role === 'owner') {
             // Owners can only edit cashiers, and only their own cashiers (scope handles this)
             return view('users.edit', ['user' => $user, 'roles' => ['kasir' => 'Cashier']]);
        }
        
        // Load stores for the admin edit form
        $stores = \App\Models\Store::all();
        return view('users.edit', ['user' => $user, 'roles' => ['admin' => 'Admin', 'owner' => 'Owner', 'kasir' => 'Cashier'], 'stores' => $stores]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,kasir,owner',
            'store_id' => 'nullable|exists:stores,id',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        
        // Admins can update the store_id 
        if (auth()->user()->role === 'admin') {
            $userData['store_id'] = $validated['store_id'] ?? null;
        }

        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
