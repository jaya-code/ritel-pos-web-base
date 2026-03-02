<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Store;

class SettingsController extends Controller
{
    public function index()
    {
        $store = Store::where('owner_id', Auth::id())->first();
        return view('settings.index', compact('store'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Bumped to 5MB
        ]);

        $store = Store::where('owner_id', Auth::id())->firstOrFail();

        $data = [
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            
            $path = $request->file('logo')->store('stores', 'public');
            $data['logo'] = $path;
        }

        $store->update($data);

        return redirect()->route('settings.index')->with('success', 'Store settings updated successfully!');
    }
}
