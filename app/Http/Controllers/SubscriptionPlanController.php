<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = \App\Models\SubscriptionPlan::all();
        return view('subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('subscription_plans.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        \App\Models\SubscriptionPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('subscription-plans.index')->with('success', 'Paket langganan berhasil ditambahkan.');
    }

    public function edit(\App\Models\SubscriptionPlan $subscriptionPlan)
    {
        return view('subscription_plans.form', ['plan' => $subscriptionPlan]);
    }

    public function update(Request $request, \App\Models\SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $subscriptionPlan->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('subscription-plans.index')->with('success', 'Paket langganan berhasil diperbarui.');
    }

    public function destroy(\App\Models\SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return redirect()->route('subscription-plans.index')->with('success', 'Paket langganan berhasil dihapus.');
    }
}
