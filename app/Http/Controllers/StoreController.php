<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $stores = Store::with('owner')->latest()->paginate(15);
        return view('stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'qris_fee' => 'nullable|numeric|min:0|max:100',
            'api_url' => 'nullable|url|max:255',
            'api_token' => 'nullable|string|max:255',
        ]);

        $store = Store::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'qris_fee' => $request->qris_fee ?? 0,
            'api_url' => $request->api_url,
            'api_token' => $request->api_token,
            'owner_id' => Auth::id(), // Temporary assignment before user management attaches an actual manager
        ]);

        // Note: we no longer automatically tie the creator as the owner. 
        // Admin manages Store, Admin manages Users.
        // We link Owner to Store via UserController now.

        return redirect()->route('stores.index')->with('success', 'Branch registered successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        if (Auth::user()->role !== 'admin') {
             abort(403, 'Unauthorized action.');
        }
        return view('stores.edit', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        if (Auth::user()->role !== 'admin') {
             abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'qris_fee' => 'nullable|numeric|min:0|max:100',
            'api_url' => 'nullable|url|max:255',
            'api_token' => 'nullable|string|max:255',
        ]);

        $storeData = [
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'qris_fee' => $request->qris_fee ?? 0,
            'api_url' => $request->api_url,
            'api_token' => $request->api_token,
        ];

        $store->update($storeData);

        return redirect()->route('stores.index')->with('success', 'Branch updated successfully!');
    }

    /**
     * Generate a new API token for the specified store.
     */
    public function generateToken(Store $store)
    {
        if (Auth::user()->role !== 'admin') {
             abort(403, 'Unauthorized action.');
        }

        $store->update([
            'api_token' => Str::random(60),
        ]);

        return back()->with('success', 'API Token generated successfully!');
    }
}
