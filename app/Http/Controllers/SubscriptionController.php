<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $plans = SubscriptionPlan::where('is_active', true)->get();
        
        $subscriptionDaysLeft = null;
        $store = $user->store;
        if ($store && $store->subscription_until) {
            $subscriptionDaysLeft = (int) Carbon::now()->diffInDays(Carbon::parse($store->subscription_until), false);
        } else {
            $subscriptionDaysLeft = -1;
        }

        // Get recent transactions
        $transactions = SubscriptionTransaction::where('user_id', $user->id)->with('plan')->latest()->take(10)->get();

        return view('subscription.index', compact('plans', 'subscriptionDaysLeft', 'transactions'));
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Define Midtrans Config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Create Transaction Record
        $transaction = SubscriptionTransaction::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'pending',
        ]);

        // Request Snap Token
        $params = [
            'transaction_details' => [
                'order_id' => 'SUB-' . $transaction->id . '-' . time(),
                'gross_amount' => (int) $plan->price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => 'PLAN-' . $plan->id,
                    'price' => (int) $plan->price,
                    'quantity' => 1,
                    'name' => 'Langganan ' . $plan->name . ' (' . $plan->duration_days . ' hari)',
                ]
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
