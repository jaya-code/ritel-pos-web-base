<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function printerSettings()
    {
        $user = Auth::user();
        return view('kasir.settings.printer', compact('user'));
    }

    public function savePrinterSettings(Request $request)
    {
        $request->validate([
            'print_method' => 'required|in:web,mate_bluetooth,mate_bluetooth_ios'
        ]);

        $user = Auth::user();
        $user->update(['print_method' => $request->print_method]);

        return redirect()->back()->with('success', 'Printer settings saved successfully.');
    }
}
