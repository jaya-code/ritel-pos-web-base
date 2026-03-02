<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate a user and return a token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Optional: Ensure the user actually belongs to a store if they are an owner/cashier
        // Admins might not strictly need a store, but POS operations usually do.
        if (!in_array($user->role, ['admin', 'owner', 'kasir'])) {
            return response()->json([
                'message' => 'Unauthorized role'
            ], 403);
        }

        // Revoke older tokens for security (optional depending on use case, but good for POS)
        $user->tokens()->delete();

        $tokenName = 'pos-app-' . $user->role;
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'store_id' => $user->store_id,
            ],
            'store' => $user->store,
            'token' => $token
        ]);
    }

    /**
     * Get the authenticated user details.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('store');
        
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
