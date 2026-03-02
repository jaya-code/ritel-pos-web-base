<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashierClosing;
use App\Models\Penjualan;
use Carbon\Carbon;

class ClosingController extends Controller
{
    public function index()
    {
        $storeId = auth()->user()->store_id;
        $userId = auth()->id();

        // Find active shift
        $activeShift = CashierClosing::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return redirect()->route('pos.index')->with('error', 'Silakan mulai shift terlebih dahulu.');
        }

        $startTime = $activeShift->created_at;

        // Fetch all successful sales since start time
        $sales = Penjualan::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('status', 'paid')
            ->where('created_at', '>=', $startTime)
            ->get();

        $totals = [
            'Tunai' => 0,
            'Qris Dinamis' => 0,
            'Qris Statis' => 0,
            'Transfer' => 0,
            'Debit' => 0,
            'Lainnya' => 0,
        ];

        foreach ($sales as $sale) {
            $metode = $sale->metode_pembayaran;
            if (array_key_exists($metode, $totals)) {
                $totals[$metode] += $sale->total;
            } else {
                if (str_contains(strtolower($metode), 'qris') && str_contains(strtolower($metode), 'dinamis')) {
                     $totals['Qris Dinamis'] += $sale->total;
                } elseif (str_contains(strtolower($metode), 'qris')) {
                     $totals['Qris Statis'] += $sale->total;
                } elseif (str_contains(strtolower($metode), 'transfer')) {
                     $totals['Transfer'] += $sale->total;
                } else {
                     $totals['Lainnya'] += $sale->total;
                }
            }
        }

        $systemCash = $activeShift->opening_cash + $totals['Tunai'];

        return view('closing.index', compact('totals', 'systemCash', 'activeShift', 'startTime', 'sales'));
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
        ]);

        $storeId = auth()->user()->store_id;
        $userId = auth()->id();

        // Check if there is already an active shift
        $activeShift = CashierClosing::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            return redirect()->route('pos.index')->with('error', 'Shift sudah berjalan.');
        }

        CashierClosing::create([
            'store_id' => $storeId,
            'user_id' => $userId,
            'opening_cash' => $request->opening_cash,
            'status' => 'open',
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift berhasil dimulai.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $storeId = auth()->user()->store_id;
        $userId = auth()->id();

        $activeShift = CashierClosing::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return redirect()->route('pos.index')->with('error', 'Tidak ada shift yang terbuka.');
        }

        $startTime = $activeShift->created_at;

        $sales = Penjualan::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('status', 'paid')
            ->where('created_at', '>=', $startTime)
            ->get();

        $totals = [
            'Tunai' => 0,
            'Qris Dinamis' => 0,
            'Qris Statis' => 0,
            'Transfer' => 0,
            'Debit' => 0,
            'Lainnya' => 0,
        ];

        foreach ($sales as $sale) {
            $metode = $sale->metode_pembayaran;
            if (array_key_exists($metode, $totals)) {
                $totals[$metode] += $sale->total;
            } else {
                if (str_contains(strtolower($metode), 'qris') && str_contains(strtolower($metode), 'dinamis')) {
                     $totals['Qris Dinamis'] += $sale->total;
                } elseif (str_contains(strtolower($metode), 'qris')) {
                     $totals['Qris Statis'] += $sale->total;
                } elseif (str_contains(strtolower($metode), 'transfer')) {
                     $totals['Transfer'] += $sale->total;
                } else {
                     $totals['Lainnya'] += $sale->total;
                }
            }
        }

        $systemCash = $activeShift->opening_cash + $totals['Tunai'];
        $actualCash = $request->actual_cash;
        $difference = $actualCash - $systemCash;

        $activeShift->update([
            'system_cash' => $systemCash,
            'actual_cash' => $actualCash,
            'difference' => $difference,
            'total_tunai' => $totals['Tunai'],
            'total_qris_dinamis' => $totals['Qris Dinamis'],
            'total_qris_statis' => $totals['Qris Statis'],
            'total_transfer' => $totals['Transfer'],
            'total_debit' => $totals['Debit'],
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        return redirect()->route('pos.index')
            ->with('success', 'Tutup Kasir berhasil dengan selisih Rp ' . number_format($difference, 0, ',', '.'))
            ->with('closing_receipt_id', $activeShift->id);
    }

    public function receiptJson($id)
    {
        $closing = CashierClosing::with('store', 'user')->findOrFail($id);
        $store = $closing->store;
        $a = [];

        // Logo image for BPrint iOS
        if (!empty($store->logo)) {
            $logo = new \stdClass;
            $logo->type = 1; // image
            $logo->path = asset('storage/' . $store->logo); // complete filepath
            $logo->align = 1; // 1 = center
            array_push($a, $logo);
        }

        $obj1 = new \stdClass(); $obj1->type = 0; $obj1->content = $store->name ?? 'R-POS Outlet';
        $obj1->bold = 1; $obj1->align = 1; $obj1->format = 2;
        $a[] = $obj1;

        $obj2 = new \stdClass(); $obj2->type = 0; $obj2->content = "LAPORAN TUTUP KASIR";
        $obj2->bold = 1; $obj2->align = 1; $obj2->format = 0;
        $a[] = $obj2;

        $obj_div = new \stdClass(); $obj_div->type = 0; $obj_div->content = '--------------------------------';
        $obj_div->bold = 0; $obj_div->align = 1; $obj_div->format = 0;
        $a[] = $obj_div;

        $info = "Waktu: " . \Carbon\Carbon::parse($closing->created_at)->format('d/m/Y H:i') . "<br />";
        $info .= "Kasir: " . ($closing->user->name ?? 'Unknown') . "<br />";

        $obj4 = new \stdClass(); $obj4->type = 0; $obj4->content = $info;
        $obj4->bold = 0; $obj4->align = 0; $obj4->format = 0;
        $a[] = $obj4;
        $a[] = $obj_div;

        $padStr = function($val) { return str_pad(number_format($val, 0, ',', '.'), 8, " ", STR_PAD_LEFT); };

        $summary = "Modal Awal:          Rp " . $padStr($closing->opening_cash) . "<br />";
        $summary .= "Penjualan Tunai:     Rp " . $padStr($closing->total_tunai) . "<br />";
        if ($closing->total_qris_dinamis > 0) $summary .= "QRIS Dinamis:        Rp " . $padStr($closing->total_qris_dinamis) . "<br />";
        if ($closing->total_qris_statis > 0) $summary .= "QRIS Statis:         Rp " . $padStr($closing->total_qris_statis) . "<br />";
        if ($closing->total_transfer > 0) $summary .= "Transfer:            Rp " . $padStr($closing->total_transfer) . "<br />";
        if ($closing->total_debit > 0) $summary .= "Debit:               Rp " . $padStr($closing->total_debit) . "<br />";
        
        $totalSystem = $closing->total_tunai + $closing->total_qris_dinamis + $closing->total_qris_statis + $closing->total_transfer + $closing->total_debit;
        $summary .= "Total Penjualan:     Rp " . $padStr($totalSystem) . "<br />";
        
        $obj5 = new \stdClass(); $obj5->type = 0; $obj5->content = $summary;
        $obj5->bold = 0; $obj5->align = 0; $obj5->format = 0;
        $a[] = $obj5;
        $a[] = $obj_div;

        $cashInfo = "Kas Fisik (Tunai):   Rp " . $padStr($closing->actual_cash) . "<br />";
        $cashInfo .= "Selisih:           " . ($closing->difference >= 0 ? "  Rp " : " -Rp ") . $padStr(abs($closing->difference)) . "<br />";

        $obj6 = new \stdClass(); $obj6->type = 0; $obj6->content = $cashInfo;
        $obj6->bold = 1; $obj6->align = 0; $obj6->format = 0;
        $a[] = $obj6;
        $a[] = $obj_div;

        $obj_empty = new \stdClass(); $obj_empty->type = 0; $obj_empty->content = ' ';
        $obj_empty->bold = 0; $obj_empty->align = 0; $obj_empty->format = 0;
        $a[] = $obj_empty; $a[] = $obj_empty;

        return response(json_encode($a))->header('Content-Type', 'application/json');
    }
}
