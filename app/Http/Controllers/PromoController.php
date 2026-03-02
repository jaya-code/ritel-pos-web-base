<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::latest()->paginate(10);
        return view('promos.index', compact('promos'));
    }

    public function create()
    {
        $products = \App\Models\Product::all(); // Will be scoped by HasStore
        return view('promos.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:simple_discount,buy_x_get_y,bundle',
            'product_id' => 'nullable|exists:products,id',
            'reward_product_id' => 'nullable|exists:products,id',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'bundle_price' => 'nullable|numeric|min:0',
            'buy_qty' => 'nullable|integer|min:1',
            'get_qty' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Auto-assign store_id via HasStore trait or manual if needed
        // but HasStore trait handles creating event to set store_id
        
        Promo::create($validated);

        return redirect()->route('promos.index')->with('success', 'Promo created successfully.');
    }

    public function edit(Promo $promo)
    {
        $products = \App\Models\Product::all();
        return view('promos.edit', compact('promo', 'products'));
    }

    public function update(Request $request, Promo $promo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:simple_discount,buy_x_get_y,bundle',
            'product_id' => 'nullable|exists:products,id',
            'reward_product_id' => 'nullable|exists:products,id',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'bundle_price' => 'nullable|numeric|min:0',
            'buy_qty' => 'nullable|integer|min:1',
            'get_qty' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $promo->update($validated);

        return redirect()->route('promos.index')->with('success', 'Promo updated successfully.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('promos.index')->with('success', 'Promo deleted successfully.');
    }
}
