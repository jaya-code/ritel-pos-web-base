<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'kasir') {
            if ($user->store && $user->store->owner) {
                if (!$user->store->owner->hasActiveSubscription()) {
                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Harap hubungi owner untuk perpanjang langganan supaya bisa melakukan transaksi.'], 403);
                    }
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->withErrors(['email' => 'Harap hubungi owner untuk perpanjang langganan supaya bisa melakukan transaksi.']);
                }
            }
        } elseif ($user->role === 'owner') {
            if (!$user->hasActiveSubscription()) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Langganan Anda telah habis. Harap perpanjang untuk mengakses fitur ini.'], 403);
                }
                return redirect()->route('subscription.index')->with('error', 'Langganan Anda telah habis. Harap perpanjang untuk mengakses fitur ini.');
            }
        }

        return $next($request);
    }
}
