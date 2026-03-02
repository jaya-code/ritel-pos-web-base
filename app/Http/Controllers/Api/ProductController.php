<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Get all products for the authenticated user's store.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $products = Product::where('store_id', $user->store_id)
            ->with('category:id,name') // Load category name to avoid N+1 and keep payload small
            ->orderBy('product_name')
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get details of a specific product.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $product = Product::where('store_id', $user->store_id)
            ->with('category:id,name')
            ->findOrFail($id);

        return response()->json([
            'product' => $product
        ]);
    }
}
