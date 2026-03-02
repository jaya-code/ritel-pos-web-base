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
            'store_name' => 'required_if:role,owner|nullable|string|max:255',
            'subscription_until' => 'nullable|date',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'store_id' => auth()->user()->store_id, // Default to creator's store (for Owners creating Cashiers)
        ];

        // Ensure only admins can set subscriptions (Will be saved to Store later)
        $subscriptionUntil = null;
        if (auth()->user()->role === 'admin' && $validated['role'] === 'owner' && !empty($validated['subscription_until'])) {
            $subscriptionUntil = $validated['subscription_until'];
        }

        // Create User
        $user = User::create($userData);

        // If Role is Owner, Create Store and Link
        if ($validated['role'] === 'owner' && !empty($validated['store_name'])) {
            $store = \App\Models\Store::create([
                'name' => $validated['store_name'],
                'owner_id' => $user->id,
                'subscription_until' => $subscriptionUntil,
            ]);

            // Update user with store_id
            $user->store_id = $store->id;
            $user->save();
        }

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
        return view('users.edit', ['user' => $user, 'roles' => ['admin' => 'Admin', 'owner' => 'Owner', 'kasir' => 'Cashier']]);
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
            'subscription_until' => 'nullable|date',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Update subscription on the Store if the user is an owner
        if ($user->role === 'owner' && $user->store) {
            if (auth()->user()->role === 'admin' && $validated['role'] === 'owner' && !empty($validated['subscription_until'])) {
                $user->store->update(['subscription_until' => $validated['subscription_until']]);
            } elseif (auth()->user()->role === 'admin' && $validated['role'] === 'owner' && empty($validated['subscription_until'])) {
                $user->store->update(['subscription_until' => null]);
            }
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
