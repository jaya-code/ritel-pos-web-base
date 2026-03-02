<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WithdrawalController extends Controller
{
    /**
     * Owner methods
     */
    public function ownerIndex()
    {
        $user = auth()->user();
        if (!$user->store) {
            return redirect()->route('dashboard.index')->with('error', 'Anda belum memiliki toko.');
        }

        $store = $user->store;
        $balance = $store->qris_balance;
        
        $withdrawals = Withdrawal::where('store_id', $store->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('withdrawals.owner_index', compact('store', 'balance', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $store = $user->store;

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        $availableBalance = $store->qris_balance;

        if ($request->amount > $availableBalance) {
            return back()->with('error', 'Saldo tidak mencukupi untuk penarikan sebesar Rp ' . number_format($request->amount, 0, ',', '.'));
        }

        // Check if there's already a pending withdrawal
        $hasPending = Withdrawal::where('store_id', $store->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Anda masih memiliki permintaan penarikan yang sedang diproses (Pending). Tunggu hingga selesai sebelum mengajukan yang baru.');
        }

        Withdrawal::create([
            'store_id' => $store->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('withdrawals.index')->with('success', 'Permintaan penarikan saldo berhasil diajukan dan sedang menunggu persetujuan Admin.');
    }

    /**
     * Admin methods
     */
    public function adminIndex(Request $request)
    {
        // Admin can see all withdrawals, latest first
        // Optional filter by store later if needed
        $withdrawals = Withdrawal::with('store')
            ->orderByRaw("FIELD(status, 'pending') DESC") // Pendings on top
            ->orderBy('created_at', 'desc')
            ->get();

        return view('withdrawals.admin_index', compact('withdrawals'));
    }

    public function approve(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Penarikan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'receipt' => 'nullable|image|max:2048', // optional proof of transfer
            'admin_note' => 'nullable|string',
        ]);

        $path = null;
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('withdrawal_receipts', 'public');
            $path = 'storage/' . $path;
        }

        $withdrawal->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'receipt_path' => $path,
        ]);

        return back()->with('success', 'Penarikan berhasil disetujui.');
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Penarikan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'admin_note' => 'required|string', // reason for rejection is required
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Penarikan berhasil ditolak.');
    }
}
