<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        Log::info('Midtrans Callback: '.$payload);

        $serverKey = config('midtrans.server_key') ?: config('services.midtrans.server_key');
        $validSignatureKey = hash('sha512', $notification->order_id.$notification->status_code.$notification->gross_amount.$serverKey);

        if ($notification->signature_key != $validSignatureKey) {
            Log::error('Midtrans Invalid Signature: '.$notification->signature_key.' vs '.$validSignatureKey);

            return response(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;

        if (str_starts_with($orderId, 'SUB-')) {
            $transactionId = explode('-', $orderId)[1];
            $subTransaction = \App\Models\SubscriptionTransaction::find($transactionId);
            if (! $subTransaction) {
                Log::error('Midtrans Subscription Transaction Not Found: '.$orderId);

                return response(['message' => 'Subscription order not found'], 404);
            }

            Log::info("Midtrans Processing Subscription: OrderID: $orderId, Status: $transactionStatus");

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($subTransaction->status !== 'success') {
                    $subTransaction->update(['status' => 'success', 'paid_at' => now()]);

                    $user = $subTransaction->user;
                    $store = $user->store;
                    $days = $subTransaction->plan->duration_days;
                    
                    if ($store) {
                        $currentUntil = $store->subscription_until ? \Carbon\Carbon::parse($store->subscription_until) : null;

                        if ($currentUntil && $currentUntil->isFuture()) {
                            $store->subscription_until = $currentUntil->addDays($days);
                        } else {
                            $store->subscription_until = now()->addDays($days);
                        }
                        $store->save();
                    }
                    Log::info("Subscription extended for Store User ID: {$user->id}");
                }
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $subTransaction->update(['status' => 'failed']);
            }

            return response(['message' => 'Success']);
        }

        $order = Penjualan::where('penjualan_id', $orderId)->first();

        if (! $order) {
            Log::error('Midtrans Order Not Found: '.$orderId);

            return response(['message' => 'Order not found'], 404);
        }

        Log::info("Midtrans Processing: OrderID: $orderId, Status: $transactionStatus");

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->update(['status' => 'pending']);
            } elseif ($fraudStatus == 'accept') {
                $order->update(['status' => 'paid']);
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update(['status' => 'paid']);
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $order->update(['status' => 'cancelled']);

            // Restore stock
            foreach ($order->details as $detail) {
                $detail->product->increment('stock', $detail->qty_jual);
            }
        } elseif ($transactionStatus == 'pending') {
            $order->update(['status' => 'pending']);
        }

        return response(['message' => 'Success']);
    }
}
