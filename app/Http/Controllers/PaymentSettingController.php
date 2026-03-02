<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;
        
        // Ensure store exists for the user
        if (!$store) {
            return redirect()->back()->with('error', 'Store not found.');
        }

        return view('owner.payment_settings.index', compact('store'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'payment_methods' => 'array',
            'qris_static_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $store = Auth::user()->store;
        $config = $store->payment_config ?? [];

        // Update enabled methods
        $config['enabled_methods'] = $request->payment_methods ?? [];

        // Handle Image Upload
        if ($request->hasFile('qris_static_image')) {
            // Delete old image if exists
            if (isset($config['qris_static_image']) && $config['qris_static_image'] && Storage::disk('public')->exists($config['qris_static_image'])) {
                Storage::disk('public')->delete($config['qris_static_image']);
            }

            $path = $request->file('qris_static_image')->store('qris_images', 'public');
            $config['qris_static_image'] = $path;
        }

        $store->update(['payment_config' => $config]);

        return redirect()->route('payment-settings.index')->with('success', 'Payment settings updated successfully.');
    }
}
