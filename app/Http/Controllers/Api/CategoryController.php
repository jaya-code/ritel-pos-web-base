<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Get all active categories for the authenticated user's store.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $categories = Category::where('store_id', $user->store_id)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }
}
