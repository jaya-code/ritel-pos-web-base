<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Get transaction history for the authenticated user's store.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $transactions = Penjualan::with(['details.product'])
            ->where('store_id', $user->store_id)
            ->latest('tgl_penjualan')
            ->paginate(50);

        return response()->json($transactions);
    }

    /**
     * Store a new transaction from the POS application.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->store_id) {
            return response()->json(['error' => 'User does not belong to a store'], 403);
        }

        $request->validate([
            'cart_data' => 'required|array|min:1',
            'cart_data.*.id' => 'required|exists:products,id',
            'cart_data.*.quantity' => 'required|numeric|min:1',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'string',
        ]);

        $cartData = $request->cart_data;
        $amountPaid = $request->amount_paid;
        $paymentMethod = $request->payment_method ?? 'Tunai';

        DB::beginTransaction();

        try {
            $totalHarga = 0;
            $totalDiscount = 0;

            foreach ($cartData as $item) {
                $product = Product::find($item['id']);
                if (!$product) continue;

                $hargaJual = $product->selling_price;
                $subtotalNormal = $hargaJual * $item['quantity'];
                $totalHarga += $subtotalNormal;

                $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
                if ($itemDiscount > $subtotalNormal) {
                    $itemDiscount = $subtotalNormal;
                }
                $totalDiscount += $itemDiscount;
            }

            $manualDiscount = $request->input('global_discount', 0);
            $totalDiscount += $manualDiscount;
            $totalPayable = $totalHarga - $totalDiscount;

            if ($amountPaid < $totalPayable && $paymentMethod === 'Tunai') {
                return response()->json(['error' => 'Pembayaran kurang!'], 400);
            }
            $uangKembali = max(0, $amountPaid - $totalPayable);

            // Generate IDs
            $penjualanId = 'TRX-' . Carbon::now()->format('YmdHis') . '-' . rand(100, 999);
            $invoice = 'INV/' . Carbon::now()->format('Ymd') . '/' . rand(1000, 9999);

            $penjualan = Penjualan::create([
                'penjualan_id' => $penjualanId,
                'invoice' => $invoice,
                'tgl_penjualan' => Carbon::now(),
                'potongan_harga' => $totalDiscount,
                'total_harga' => $totalHarga,
                'jumlah_uang' => $amountPaid,
                'uang_kembali' => $uangKembali,
                'user_id' => $user->id,
                'total' => $totalPayable,
                'metode_pembayaran' => $paymentMethod,
                'status' => 'paid',
                'store_id' => $user->store_id,
            ]);

            foreach ($cartData as $item) {
                $product = Product::find($item['id']);
                if (!$product) continue;

                $hargaJual = $product->selling_price;
                $qty = $item['quantity'];
                
                $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
                $subtotalNormal = $hargaJual * $qty;
                if ($itemDiscount > $subtotalNormal) {
                    $itemDiscount = $subtotalNormal;
                }
                $totalFinalItem = $subtotalNormal - $itemDiscount;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualanId,
                    'product_id' => $product->id,
                    'qty_jual' => $qty,
                    'harga_beli' => $product->cost_price ?? 0,
                    'harga_jual' => $hargaJual,
                    'sub_total' => $subtotalNormal,
                    'diskon' => $itemDiscount,
                    'total' => $totalFinalItem,
                ]);

                // Decrement stock
                $product->decrement('stock', $qty);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaction successful',
                'transaction' => $penjualan->load('details.product')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }
}
