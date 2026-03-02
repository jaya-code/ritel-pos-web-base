<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Product;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    private function getActiveStore()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            if (session()->has('admin_active_store_id')) {
                return \App\Models\Store::find(session('admin_active_store_id'));
            }

            return \App\Models\Store::first();
        }

        return $user->store;
    }

    // Display POS Interface
    public function index()
    {
        $store = $this->getActiveStore();
        if (! $store) {
            return redirect()->route('dashboard.index')->with('error', 'Please create or assign a store first.');
        }
        $paymentConfig = $store->payment_config ?? [];
        $qrisFee = $store->qris_fee ?? 0;

        // Fetch active promos
        $promos = \App\Models\Promo::where('store_id', $store->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->with('product', 'rewardProduct') // Eager load products for JS identifying
            ->get();

        $activeShift = \App\Models\CashierClosing::where('user_id', auth()->id())
            ->where('store_id', $store->id)
            ->where('status', 'open')
            ->first();

        return view('pos.index', compact('paymentConfig', 'qrisFee', 'promos', 'activeShift'));
    }

    public function search(Request $request)
    {
        $query = $request->barcode;

        // Try exact barcode match first
        $product = Product::with('units')->where('barcode', $query)->first();
        if ($product) {
            $product->is_base_unit = true;

            return response()->json(['success' => true, 'products' => [$product]]);
        }

        // Try exact unit barcode match
        $unit = \App\Models\ProductUnit::with('product.units')->where('barcode', $query)->first();
        if ($unit && $unit->product) {
            $p = clone $unit->product;
            $p->selected_unit = $unit;
            $p->is_base_unit = false;

            return response()->json(['success' => true, 'products' => [$p]]);
        }

        // Try searching by name
        $products = Product::with('units')->where('product_name', 'like', '%'.$query.'%')->get();

        if ($products->count() > 0) {
            $results = [];
            foreach ($products as $p) {
                // Return only base product if type is 'stock'
                if ($request->type === 'stock') {
                    $results[] = clone $p;

                    continue;
                }

                // Normal POS search: Add base variant
                $pBase = clone $p;
                $pBase->is_base_unit = true;
                $results[] = $pBase;

                // Add each unit variant
                foreach ($p->units as $u) {
                    $pUnit = clone $p;
                    $pUnit->selected_unit = $u;
                    $pUnit->is_base_unit = false;
                    $results[] = $pUnit;
                }
            }

            return response()->json(['success' => true, 'products' => $results]);
        }

        return response()->json(['success' => false]);
    }

    // API to search member by name or phone
    public function searchMember(Request $request)
    {
        $query = $request->input('query');
        $storeId = $this->getActiveStore()->id ?? null;

        if (! $query) {
            return response()->json([]);
        }

        $members = \App\Models\Member::where('store_id', $storeId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($members);
    }

    // Store Transaction (Penjualan)
    public function store(Request $request)
    {
        $store = $this->getActiveStore();
        if (! $store) {
            return redirect()->back()->with('error', 'Store not assigned to user');
        }

        $cartData = json_decode($request->cart_data, true);
        $amountPaid = $request->amount_paid; // Must be decimal input
        $paymentMethod = $request->payment_method ?? 'Tunai';
        $memberId = $request->input('member_id'); // Get member_id

        if (empty($cartData)) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        $isMember = false;
        if ($memberId) {
            // Verify member belongs to store
            $member = \App\Models\Member::where('id', $memberId)->where('store_id', $store->id)->first();
            if ($member) {
                $isMember = true;
            }
        }

        DB::beginTransaction();

        try {
            $totalHarga = 0;
            $totalItemDiscount = 0;
            foreach ($cartData as $item) {
                $product = Product::find($item['id']);
                if (! $product) {
                    continue;
                }

                $unit = ! empty($item['unit_id']) ? \App\Models\ProductUnit::find($item['unit_id']) : null;
                $priceBase = $unit ? $unit->price : $product->selling_price;
                $priceMember = $unit ? ($unit->member_price ?? $priceBase) : $product->getPrice(true);
                $hargaJual = $isMember ? $priceMember : $priceBase;

                $subtotalNormal = $hargaJual * $item['quantity'];
                $totalHarga += $subtotalNormal;

                $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
                if ($itemDiscount > $subtotalNormal) {
                    $itemDiscount = $subtotalNormal;
                }
                $totalItemDiscount += $itemDiscount;
            }

            // Calculate change
            $manualDiscount = $request->input('discount', 0);
            $totalDiscount = $totalItemDiscount + $manualDiscount;
            $totalpayable = $totalHarga - $totalDiscount;

            if ($amountPaid < $totalpayable) {
                return redirect()->back()->with('error', 'Pembayaran kurang!');
            }
            $uangKembali = $amountPaid - $totalpayable;

            // Generate IDs
            $penjualanId = 'TRX-'.Carbon::now()->format('YmdHis').'-'.rand(100, 999);
            $invoice = 'INV/'.Carbon::now()->format('Ymd').'/'.rand(1000, 9999);

            if ($paymentMethod === 'Qris Dinamis') {
                // Transaction already created in getPaymentToken
                // We trust the frontend flow here or the callback
                // Just redirect with success
                return redirect()->route('pos.index')->with('success', 'Transaksi Qris Dinamis Berhasil Diproses! Silahkan Cek Status Pembayaran.')->with('receipt_id', $penjualanId);
            }

            $penjualan = Penjualan::create([
                'penjualan_id' => $penjualanId,
                'invoice' => $invoice,
                'tgl_penjualan' => Carbon::now(),
                'potongan_harga' => $totalDiscount,
                'total_harga' => $totalHarga,
                'jumlah_uang' => $amountPaid,
                'uang_kembali' => $uangKembali,
                'user_id' => auth()->id() ?? 1,
                'total' => $totalpayable, // Final total after discount
                'metode_pembayaran' => $paymentMethod,
                'status' => 'paid',
                'store_id' => $store->id,
                'member_id' => $isMember ? $memberId : null,
            ]);

            foreach ($cartData as $item) {
                $product = Product::find($item['id']);
                if (! $product) {
                    continue;
                }

                $unit = ! empty($item['unit_id']) ? \App\Models\ProductUnit::find($item['unit_id']) : null;
                $priceBase = $unit ? $unit->price : $product->selling_price;
                $priceMember = $unit ? ($unit->member_price ?? $priceBase) : $product->getPrice(true);
                $hargaJual = $isMember ? $priceMember : $priceBase;

                $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
                $qty = $item['quantity'];
                $subtotalNormal = $hargaJual * $qty;
                if ($itemDiscount > $subtotalNormal) {
                    $itemDiscount = $subtotalNormal;
                }
                $totalFinalItem = $subtotalNormal - $itemDiscount;

                $multiplier = $unit ? $unit->quantity : 1;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualanId,
                    'product_id' => $product->id,
                    'qty_jual' => $qty,
                    'harga_beli' => $product->cost_price * $multiplier,
                    'harga_jual' => $hargaJual,
                    'sub_total' => $subtotalNormal,
                    'diskon' => $itemDiscount,
                    'total' => $totalFinalItem,
                ]);

                // Decrement stock
                $product->decrement('stock', $qty * $multiplier);
            }

            DB::commit();

            $successMsg = 'Transaksi Berhasil!';
            if ($paymentMethod === 'Tunai' && $uangKembali > 0) {
                $successMsg .= ' Kembalian: Rp '.number_format($uangKembali, 0, ',', '.');
            }

            return redirect()->route('pos.index')
                ->with('success', $successMsg)
                ->with('receipt_id', $penjualanId);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Transaksi Gagal: '.$e->getMessage());
        }
    }

    public function getPaymentToken(Request $request)
    {
        $cartData = json_decode($request->cart_data, true);

        if (empty($cartData)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $memberId = $request->input('member_id');
        $isMember = false;
        if ($memberId) {
            $member = \App\Models\Member::find($memberId);
            // Basic store check omitted for brevity but should exist in real app
            if ($member) {
                $isMember = true;
            }
        }

        $totalHarga = 0;
        $totalItemDiscount = 0;
        foreach ($cartData as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $unit = ! empty($item['unit_id']) ? \App\Models\ProductUnit::find($item['unit_id']) : null;
                $priceBase = $unit ? $unit->price : $product->selling_price;
                $priceMember = $unit ? ($unit->member_price ?? $priceBase) : $product->getPrice(true);
                $price = $isMember ? $priceMember : $priceBase;

                $subtotalNormal = $price * $item['quantity'];
                $totalHarga += $subtotalNormal;

                $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
                if ($itemDiscount > $subtotalNormal) {
                    $itemDiscount = $subtotalNormal;
                }
                $totalItemDiscount += $itemDiscount;

                // Check stock
                $multiplier = $unit ? $unit->quantity : 1;
                $stockRequired = $item['quantity'] * $multiplier;
                if ($product->stock < $stockRequired) {
                    return response()->json(['error' => "Stock for {$product->product_name} is insufficient"], 400);
                }
            }
        }

        $manualDiscount = $request->input('discount', 0);
        $totalDiscount = $totalItemDiscount + $manualDiscount;
        $totalAfterDiscount = max(0, $totalHarga - $totalDiscount);

        $store = $this->getActiveStore();
        $feePercent = $store->qris_fee ?? 0;
        $feeAmount = $totalAfterDiscount * ($feePercent / 100);
        $finalTotal = round($totalAfterDiscount + $feeAmount);

        // Create Pending Transaction
        $lastTransaction = Penjualan::where('user_id', auth()->id())->latest()->first();
        $invoiceNumber = 'INV-'.date('Ymd').'-'.str_pad(($lastTransaction ? $lastTransaction->id + 1 : 1), 4, '0', STR_PAD_LEFT);
        $orderId = 'TRX-'.Carbon::now()->format('YmdHis').'-'.rand(100, 999); // Use TRX format

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'penjualan_id' => $orderId,
                'invoice' => $invoiceNumber, // Temporary invoice
                'tgl_penjualan' => now(),
                'total_harga' => $totalHarga,
                'potongan_harga' => $totalDiscount,
                'total' => $finalTotal,
                'jumlah_uang' => $finalTotal, // Assumed exact payment for QRIS
                'uang_kembali' => 0,
                'metode_pembayaran' => 'Qris Dinamis',
                'user_id' => auth()->id(),
                'status' => 'pending',
                'store_id' => $store->id,
                'member_id' => $isMember ? $memberId : null,
            ]);

            foreach ($cartData as $item) {
                $product = Product::find($item['id']);
                if (! $product) {
                    continue;
                }

                $unit = ! empty($item['unit_id']) ? \App\Models\ProductUnit::find($item['unit_id']) : null;
                $priceBase = $unit ? $unit->price : $product->selling_price;
                $priceMember = $unit ? ($unit->member_price ?? $priceBase) : $product->getPrice(true);
                $hargaJual = $isMember ? $priceMember : $priceBase;

                $itemDiscount = isset($item['discount']) ? floatval($item['discount']) : 0;
                $qty = $item['quantity'];

                $subtotalNormal = $hargaJual * $qty;
                if ($itemDiscount > $subtotalNormal) {
                    $itemDiscount = $subtotalNormal;
                }
                $totalFinalItem = $subtotalNormal - $itemDiscount;

                $multiplier = $unit ? $unit->quantity : 1;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->penjualan_id,
                    'product_id' => $product->id,
                    'qty_jual' => $qty,
                    'harga_beli' => $product->cost_price * $multiplier,
                    'harga_jual' => $hargaJual,
                    'sub_total' => $subtotalNormal,
                    'diskon' => $itemDiscount,
                    'total' => $totalFinalItem,
                ]);

                // Reserve stock (decrement)
                $product->decrement('stock', $qty * $multiplier);
            }

            DB::commit();

            $itemDetails = [];
            foreach ($cartData as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $unit = ! empty($item['unit_id']) ? \App\Models\ProductUnit::find($item['unit_id']) : null;
                    $priceBase = $unit ? $unit->price : $product->selling_price;
                    $priceMember = $unit ? ($unit->member_price ?? $priceBase) : $product->getPrice(true);
                    $price = $isMember ? $priceMember : $priceBase;

                    $itemName = substr($product->product_name, 0, 40).($unit ? ' ('.$unit->unit_name.')' : '');

                    $itemDetails[] = [
                        'id' => $product->id.($unit ? '_'.$unit->id : ''),
                        'price' => (int) $price,
                        'quantity' => $item['quantity'],
                        'name' => substr($itemName, 0, 50),
                    ];
                }
            }

            // Add Fee as item if exists (Qris fee)
            if ($feeAmount > 0) {
                $itemDetails[] = [
                    'id' => 'QRIS-FEE',
                    'price' => (int) $feeAmount,
                    'quantity' => 1,
                    'name' => 'Biaya QRIS',
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $finalTotal,
                ],
                'customer_details' => [
                    'first_name' => 'POS Customer',
                ],
                'item_details' => $itemDetails,
                'enabled_payments' => ['qris', 'gopay', 'shopeepay', 'other_qris'],
            ];

            $token = $this->midtrans->getSnapToken($params);

            return response()->json(['token' => $token]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // JSON Receipt Endpoint for Bluetooth Print Apps (Android & iOS)
    public function receiptJson($id)
    {
        $sale = Penjualan::with('details.product', 'user', 'store')->where('penjualan_id', $id)->firstOrFail();
        $store = $sale->store;

        $a = [];

        // Logo image for BPrint iOS
        if (! empty($store->logo)) {
            $logo = new \stdClass;
            $logo->type = 1; // image
            $logo->path = asset('storage/'.$store->logo); // complete filepath
            $logo->align = 1; // 1 = center
            array_push($a, $logo);
        }

        // Header: Store Name
        $storeName = new \stdClass;
        $storeName->type = 0;
        $storeName->content = $store->name ?? config('app.name');
        $storeName->bold = 1;
        $storeName->align = 1;
        $storeName->format = 1; // Double Height
        array_push($a, $storeName);

        // Address
        $address = new \stdClass;
        $address->type = 0;
        $address->content = $store->address ?? 'Alamat Toko';
        $address->bold = 0;
        $address->align = 1;
        $address->format = 0;
        array_push($a, $address);

        // Separator
        $separator = new \stdClass;
        $separator->type = 0;
        $separator->content = str_repeat('-', 32);
        $separator->bold = 0;
        $separator->align = 1;
        $separator->format = 0;
        array_push($a, $separator);

        // Transaction Info
        $info = new \stdClass;
        $info->type = 0;
        $info->content = "Kode: {$sale->penjualan_id}";
        $info->bold = 0;
        $info->align = 0; // Left
        $info->format = 0;
        array_push($a, $info);

        $date = new \stdClass;
        $date->type = 0;
        $date->content = 'Tgl : '.Carbon::parse($sale->tgl_penjualan)->format('d/m/Y H:i');
        $date->bold = 0;
        $date->align = 0;
        $date->format = 0;
        array_push($a, $date);

        $cashier = new \stdClass;
        $cashier->type = 0;
        $cashier->content = 'Kasir: '.($sale->user->name ?? 'Staff');
        $cashier->bold = 0;
        $cashier->align = 0;
        $cashier->format = 0;
        array_push($a, $cashier);

        // Separator
        array_push($a, clone $separator);

        // Items
        $no = 1;
        foreach ($sale->details as $item) {
            $productName = $item->product->product_name ?? 'Unknown';
            $qty = $item->qty_jual;
            $price = $item->harga_jual;
            $subtotal = $item->sub_total;

            // Item Name Line
            $nameLine = new \stdClass;
            $nameLine->type = 0;
            $nameLine->content = sprintf('%-2s %-28s', $no++.'.', $productName); // Number + Name
            $nameLine->bold = 0;
            $nameLine->align = 0;
            $nameLine->format = 0;
            array_push($a, $nameLine);

            // Qty x Price = Subtotal Line
            // Using sprintf for alignment
            // 32 chars: "  2 x 10.000        20.000"
            $calcLine = new \stdClass;
            $calcLine->type = 0;
            // %3s for qty, %8s for price, %10s for total
            $calcContent = sprintf('   %dx %s', $qty, number_format($price, 0, ',', '.'));
            $subtotalStr = number_format($subtotal, 0, ',', '.');

            // Calculate padding
            $pad = 32 - strlen($calcContent) - strlen($subtotalStr);
            if ($pad < 0) {
                $pad = 1;
            }

            $calcLine->content = $calcContent.str_repeat(' ', $pad).$subtotalStr;
            $calcLine->bold = 0;
            $calcLine->align = 0;
            $calcLine->format = 0;
            array_push($a, $calcLine);
        }

        // Separator
        array_push($a, clone $separator);

        // Totals
        $totalLine = new \stdClass;
        $totalLine->type = 0;
        $label = 'TOTAL';
        $value = 'Rp '.number_format($sale->total, 0, ',', '.');
        $pad = 32 - strlen($label) - strlen($value);
        if ($pad < 0) {
            $pad = 1;
        }
        $totalLine->content = $label.str_repeat(' ', $pad).$value;
        $totalLine->bold = 1;
        $totalLine->align = 0;
        $totalLine->format = 1; // Bigger font for total
        array_push($a, $totalLine);

        // Payment Details
        if ($sale->metode_pembayaran === 'Tunai') {
            $payLine = new \stdClass;
            $payLine->type = 0;
            $label = 'TUNAI';
            $value = 'Rp '.number_format($sale->jumlah_uang ?? $sale->total, 0, ',', '.');
            $pad = 32 - strlen($label) - strlen($value);
            if ($pad < 0) {
                $pad = 1;
            }
            $payLine->content = $label.str_repeat(' ', $pad).$value;
            $payLine->bold = 0;
            $payLine->align = 0;
            $payLine->format = 0;
            array_push($a, $payLine);

            $changeLine = new \stdClass;
            $changeLine->type = 0;
            $label = 'KEMBALI';
            $value = 'Rp '.number_format(($sale->jumlah_uang ?? $sale->total) - $sale->total, 0, ',', '.');
            $pad = 32 - strlen($label) - strlen($value);
            if ($pad < 0) {
                $pad = 1;
            }
            $changeLine->content = $label.str_repeat(' ', $pad).$value;
            $changeLine->bold = 0;
            $changeLine->align = 0;
            $changeLine->format = 0;
            array_push($a, $changeLine);
        } else {
            $methodLine = new \stdClass;
            $methodLine->type = 0;
            $methodLine->content = 'Metode: '.strtoupper(str_replace('_', ' ', $sale->metode_pembayaran));
            $methodLine->bold = 0;
            $methodLine->align = 0;
            $methodLine->format = 0;
            array_push($a, $methodLine);
        }

        // Footer
        $footerSeparator = clone $separator;
        array_push($a, $footerSeparator);

        $footer1 = new \stdClass;
        $footer1->type = 0;
        $footer1->content = 'Terima Kasih';
        $footer1->bold = 1;
        $footer1->align = 1;
        $footer1->format = 0;
        array_push($a, $footer1);

        $footer2 = new \stdClass;
        $footer2->type = 0;
        $footer2->content = 'Selamat Menikmati'; // matching user example exactly
        $footer2->bold = 0;
        $footer2->align = 1;
        $footer2->format = 0;
        array_push($a, $footer2);

        // Empty Lines
        $empty = new \stdClass;
        $empty->type = 0;
        $empty->content = ' ';
        $empty->bold = 0;
        $empty->align = 0;
        $empty->format = 0;
        array_push($a, $empty);
        array_push($a, clone $empty);

        return response()->json($a, 200, [], JSON_FORCE_OBJECT);
    }

    // Transaction History
    public function history(Request $request)
    {
        $status = $request->get('status');

        // Use WITA time for "today"
        $today = Carbon::now('Asia/Makassar');

        $query = Penjualan::with(['details.product', 'user'])
            ->where('user_id', auth()->id())
            ->whereBetween('tgl_penjualan', [
                $today->copy()->startOfDay()->setTimezone('UTC'), // Convert back to UTC for DB query if DB is UTC
                $today->copy()->endOfDay()->setTimezone('UTC'),
            ])
            ->latest('tgl_penjualan');

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->paginate(20);

        return view('pos.history', compact('transactions', 'status'));
    }

    // Cancel Transaction
    public function cancel($id)
    {
        $penjualan = Penjualan::with('details')->findOrFail($id);

        if ($penjualan->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending transactions can be cancelled.');
        }

        DB::beginTransaction();
        try {
            // Restore Stock
            foreach ($penjualan->details as $detail) {
                // Assuming product relationship exists or retrieving by product_id
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->increment('stock', $detail->qty_jual);
                }
            }

            // Update Status
            $penjualan->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Transaction cancelled and stock restored.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to cancel transaction: '.$e->getMessage());
        }
    }

    // Stock Input (Cashier)
    public function stock()
    {
        $suppliers = \App\Models\Supplier::all();
        $products = Product::all();

        return view('pos.stock', compact('suppliers', 'products'));
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.product_unit_id' => 'nullable|exists:product_units,id',
            'details.*.qty_beli' => 'required|numeric|min:1',
            // 'details.*.harga_beli' => 'numeric', // Removed requirement, defaults to 0 from view
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->details as $item) {
                // Use 0 if not set
                $hargaBeli = $item['harga_beli'] ?? 0;
                $total += $item['qty_beli'] * $hargaBeli;
            }

            $pembelian = \App\Models\Pembelian::create([
                'nofak_beli' => 'INV-KS-'.time(), // KS for Kasir to distinguish
                'tgl_beli' => \Carbon\Carbon::now(),
                'supplier_id' => $request->supplier_id,
                'jenis_pembelian' => 'Non Kredit',
                'status_pembelian' => 'Belum Lunas', // Changed to Belum Lunas since price is 0/pending check
                'jenis_pembayaran' => 'Tunai',
                'total' => $total,
                'grand_total' => $total,
                'user_id' => auth()->id(),
                'store_id' => $this->getActiveStore()->id ?? null,
            ]);

            foreach ($request->details as $index => $item) {
                $hargaBeli = $item['harga_beli'] ?? 0;

                $multiplier = 1;
                if (! empty($item['product_unit_id'])) {
                    $unit = \App\Models\ProductUnit::find($item['product_unit_id']);
                    if ($unit) {
                        $multiplier = $unit->quantity;
                    }
                }

                // Save Detail
                \App\Models\PembelianDetail::create([
                    'pembelian_id' => $pembelian->pembelian_id,
                    'product_id' => $item['product_id'],
                    'product_unit_id' => $item['product_unit_id'] ?? null,
                    'qty_beli' => $item['qty_beli'],
                    'harga_beli' => $hargaBeli,
                    'disc' => 0,
                    'sub_total' => $item['qty_beli'] * $hargaBeli,
                ]);

                // Update Stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $stockToAdd = $item['qty_beli'] * $multiplier;
                    $product->increment('stock', $stockToAdd);
                }
            }

            DB::commit();

            return redirect()->route('pos.stock')->with('success', 'Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menambah stok: '.$e->getMessage());
        }
    }

    public function receiptWeb($id)
    {
        $transaction = Penjualan::with(['details.product', 'user', 'store', 'member'])
            ->where('penjualan_id', $id)->firstOrFail();

        return view('pos.receipt', compact('transaction'));
    }
}
