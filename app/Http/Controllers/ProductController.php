<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|unique:products|max:255',
            'sku' => 'nullable|max:255',
            'product_name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'kode_rak' => 'nullable|max:255',
            'periode_return' => 'nullable|integer',
            'satuan' => 'required|in:pcs,liter,kg,box,bks,rtg,btl,pck,tpk,ktk',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'member_price' => 'nullable|numeric',
            'stock' => 'required|numeric',
            'stock_min' => 'required|numeric',
            'isi' => 'nullable|numeric',
            'isi' => 'nullable|numeric',
            'units' => 'nullable|array',
            'units.*.unit_name' => 'required_with:units|string|max:255',
            'units.*.quantity' => 'required_with:units|integer|min:2',
            'units.*.barcode' => 'nullable|string|max:255',
            'units.*.price' => 'required_with:units|numeric|min:0',
            'units.*.member_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::create(Arr::except($validated, ['units']));

        if (!empty($validated['units'])) {
            // Filter out empty rows if JS accidentally sent them
            $units = array_filter($validated['units'], function($u) {
                return !empty($u['unit_name']) && !empty($u['quantity']) && !empty($u['price']);
            });
            if (count($units) > 0) {
                $product->units()->createMany($units);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('units');
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'barcode' => 'required|max:255|unique:products,barcode,' . $product->id,
            'sku' => 'nullable|max:255',
            'product_name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'kode_rak' => 'nullable|max:255',
            'periode_return' => 'nullable|integer',
            'satuan' => 'required|in:pcs,liter,kg,box,bks,rtg,btl,pck,tpk,ktk',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'member_price' => 'nullable|numeric',
            'stock' => 'required|numeric',
            'stock_min' => 'required|numeric',
            'isi' => 'nullable|numeric',
            'isi' => 'nullable|numeric',
            'units' => 'nullable|array',
            'units.*.unit_name' => 'required_with:units|string|max:255',
            'units.*.quantity' => 'required_with:units|integer|min:2',
            'units.*.barcode' => 'nullable|string|max:255',
            'units.*.price' => 'required_with:units|numeric|min:0',
            'units.*.member_price' => 'nullable|numeric|min:0',
        ]);

        $product->update(Arr::except($validated, ['units']));
        
        $product->units()->delete();
        if (!empty($validated['units'])) {
            $units = array_filter($validated['units'], function($u) {
                return !empty($u['unit_name']) && !empty($u['quantity']) && !empty($u['price']);
            });
            if (count($units) > 0) {
                $product->units()->createMany($units);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
