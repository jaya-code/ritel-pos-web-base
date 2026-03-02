<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;

class PenjualanController extends Controller
{
    public function index()
    {
        $transactions = Penjualan::latest()->paginate(15);
        return view('penjualan.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Penjualan::with(['details.product', 'user'])->where('penjualan_id', $id)->firstOrFail();
        return view('penjualan.show', compact('transaction'));
    }
}
