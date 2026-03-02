<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelian = Pembelian::with('supplier')->latest('tgl_beli')->paginate(15);
        return view('pembelian.index', compact('pembelian'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::with('units')->get(); // Load units for frontend selection
        return view('pembelian.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.product_unit_id' => 'nullable|exists:product_units,id',
            'details.*.qty_beli' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($request->details as $item) {
                $subtotal = $item['qty_beli'] * $item['harga_beli'];
                $total += $subtotal;
            }

            // Simple calculation logic, can be expanded
            $grand_total = $total; 

            $pembelian = Pembelian::create([
                'nofak_beli' => 'INV-' . time(), // Simple invoice gen
                'tgl_beli' => now(),
                'supplier_id' => $request->supplier_id,
                'jenis_pembelian' => 'Non Kredit',
                'status_pembelian' => 'Lunas', // Default simplified
                'jenis_pembayaran' => 'Tunai',
                'total' => $total,
                'grand_total' => $grand_total,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->details as $item) {
                // Determine multiplier
                $multiplier = 1;
                if (!empty($item['product_unit_id'])) {
                    $unit = \App\Models\ProductUnit::find($item['product_unit_id']);
                    if ($unit) {
                        $multiplier = $unit->quantity;
                    }
                }

                // Save Detail
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->pembelian_id,
                    'product_id' => $item['product_id'],
                    'product_unit_id' => $item['product_unit_id'] ?? null,
                    'qty_beli' => $item['qty_beli'],
                    'harga_beli' => $item['harga_beli'],
                    'disc' => 0,
                    'sub_total' => $item['qty_beli'] * $item['harga_beli'], // Subtotal is based on purchase row qty
                ]);

                // Update Stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $stockToAdd = $item['qty_beli'] * $multiplier;
                    $product->increment('stock', $stockToAdd);
                    // Optional: Update cost price
                    // $product->update(['cost_price' => $item['harga_beli']]);
                }
            }

            DB::commit();

            return redirect()->route('pembelian.index')->with('success', 'Purchase recorded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording purchase: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with(['details.product', 'details.productUnit', 'supplier', 'user'])->findOrFail($id);
        return view('pembelian.show', compact('pembelian'));
    }

    public function edit($id)
    {
        $pembelian = Pembelian::with(['details.product.units', 'supplier'])->findOrFail($id);
        $suppliers = Supplier::all();
        $products = Product::with('units')->get();
        return view('pembelian.edit', compact('pembelian', 'suppliers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.product_unit_id' => 'nullable|exists:product_units,id',
            'details.*.qty_beli' => 'required|numeric|min:1',
            'details.*.harga_beli' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $pembelian = Pembelian::with('details')->findOrFail($id);

            // 1. Revert Old Stock
            foreach ($pembelian->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $oldMultiplier = 1;
                    if ($detail->product_unit_id) {
                        $oldUnit = clone $detail->productUnit; // Might need separate query if relationship not loaded
                        if (!$oldUnit) {
                           $oldUnit = \App\Models\ProductUnit::find($detail->product_unit_id);
                        }
                        if ($oldUnit) {
                            $oldMultiplier = $oldUnit->quantity;
                        }
                    }
                    $stockToRemove = $detail->qty_beli * $oldMultiplier;
                    $product->decrement('stock', $stockToRemove);
                }
            }

            // 2. Delete Old Details
            $pembelian->details()->delete();

            // 3. Calculate New Total
            $total = 0;
            foreach ($request->details as $item) {
                $subtotal = $item['qty_beli'] * $item['harga_beli'];
                $total += $subtotal;
            }
            $grand_total = $total - ($request->diskon ?? 0) + ($request->ppn ?? 0);

            // 4. Update Pembelian Record
            $pembelian->update([
                'tgl_beli' => $request->tgl_beli,
                'supplier_id' => $request->supplier_id,
                'jenis_pembelian' => $request->jenis_pembelian,
                'status_pembelian' => $request->status_pembelian ?? 'Lunas',
                'tgl_jatuh_tempo' => $request->jenis_pembelian == 'Kredit' ? $request->tgl_jatuh_tempo : null,
                'total' => $total,
                'grand_total' => $grand_total,
                // user_id stays the same or update to modifier? usually stays creator or add updated_by
            ]);

            // 5. Create New Details & Update Stock
            foreach ($request->details as $item) {
                $newMultiplier = 1;
                if (!empty($item['product_unit_id'])) {
                    $unit = \App\Models\ProductUnit::find($item['product_unit_id']);
                    if ($unit) {
                        $newMultiplier = $unit->quantity;
                    }
                }

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->pembelian_id,
                    'product_id' => $item['product_id'],
                    'product_unit_id' => $item['product_unit_id'] ?? null,
                    'qty_beli' => $item['qty_beli'],
                    'harga_beli' => $item['harga_beli'],
                    'disc' => 0,
                    'sub_total' => $item['qty_beli'] * $item['harga_beli'],
                ]);

                // Update Stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $stockToAdd = $item['qty_beli'] * $newMultiplier;
                    $product->increment('stock', $stockToAdd);
                    // Optional: Update cost price to latest purchase price
                     $product->update(['cost_price' => $item['harga_beli']]);
                }
            }

            DB::commit();

            return redirect()->route('pembelian.index')->with('success', 'Purchase updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }
}
