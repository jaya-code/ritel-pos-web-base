<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('kasir.opname', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_stock' => 'required|numeric',
            'details.*.actual_stock' => 'required|numeric|min:0',
            'details.*.note' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->details as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) continue;

                $systemStock = $item['system_stock'];
                $actualStock = $item['actual_stock'];
                $diff = $actualStock - $systemStock;

                // Create Opname Log
                StockOpname::create([
                    'store_id' => auth()->user()->store_id,
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'system_stock' => $systemStock,
                    'actual_stock' => $actualStock,
                    'difference' => $diff,
                    'note' => $item['note'] ?? null,
                ]);

                // Update actual stock 
                $product->update(['stock' => $actualStock]);
            }

            DB::commit();

            return redirect()->route('pos.opname')->with('success', 'Stock Opname berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses Stock Opname: ' . $e->getMessage());
        }
    }
}
