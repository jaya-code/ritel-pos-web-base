<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);

        $store = Store::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'owner_id' => Auth::id(),
        ]);

        // Assign store to current user (Owner)
        $user = User::find(Auth::id());
        $user->store_id = $store->id;
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Store registered successfully!');
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
            'subscription_until' => 'nullable|date',
        ]);

        $storeData = [
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'qris_fee' => $request->qris_fee ?? 0,
        ];

        if ($request->has('subscription_until')) {
            $storeData['subscription_until'] = $request->subscription_until;
        }

        $store->update($storeData);

        return redirect()->route('stores.index')->with('success', 'Store updated successfully!');
    }
}
